<?php

namespace App\Http\Controllers;

use App\Data\OrderData;
use App\Data\OrderResponseData;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends AbstractController
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
    ) {
        parent::__construct($this->orderRepository);
    }

    protected function viewPath(): string
    {
        return 'Orders';
    }

    protected function routeName(): string
    {
        return 'orders';
    }

    protected function langKey(): string
    {
        return 'order';
    }

    /**
     * Display order history list with filters and stats.
     */
    public function index(Request $request): Response
    {
        $filters = [
            'status'         => $request->status ?? 'all',
            'search'         => $request->search ?? '',
            'staff_id'       => $request->staff_id ?? null,
            'sort_by'        => $request->sort_by ?? 'created_at',
            'sort_direction' => $request->sort_direction ?? 'desc',
        ];

        $orders = $this->orderRepository->paginate(15, ['*'], $filters)
            ->through(fn (Order $order) => OrderResponseData::fromModel(
                $order->loadMissing(['items', 'staff'])
            )->toArray());

        $stats = [
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::whereIn('status', ['completed', 'paid'])->count(),
            'total_revenue'    => round((float) Order::whereIn('status', ['completed', 'paid'])->sum('total_amount'), 2),
        ];

        return Inertia::render("{$this->viewPath()}/Index", [
            'orders'  => $orders,
            'filters' => $filters,
            'stats'   => $stats,
        ]);
    }

    /**
     * Display a single order receipt / detail view.
     */
    public function show(int $id): Response
    {
        $order = $this->orderRepository->findOrFail($id, ['*'], ['items', 'staff', 'orderDiscounts.discount']);

        return Inertia::render("{$this->viewPath()}/Show", [
            'order' => $order,
        ]);
    }

    /**
     * Store a newly created order.
     * Transactional, stock-locking, price snapshotting, and discount processing.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $orderData = OrderData::from($request->all());

        $order = DB::transaction(function () use ($orderData) {
            // 1. Group items by product_id to sum requested quantities
            $itemMap = [];
            foreach ($orderData->items as $item) {
                $itemMap[$item->product_id] = ($itemMap[$item->product_id] ?? 0) + $item->quantity;
            }

            if (empty($itemMap)) {
                throw ValidationException::withMessages([
                    'items' => __('order.empty_items'),
                ]);
            }

            // 2. Lock products for update and validate stock
            $productIds = array_keys($itemMap);
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $orderItemsToCreate = [];
            $orderSubtotal = 0.00;

            foreach ($itemMap as $productId => $requestedQty) {
                /** @var Product|null $product */
                $product = $products->get($productId);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => __('order.errors.product_not_found', ['id' => $productId]),
                    ]);
                }

                if ($product->stock < $requestedQty) {
                    throw ValidationException::withMessages([
                        'items' => __('order.errors.stock_insufficient', [
                            'product'   => $product->name,
                            'available' => $product->stock,
                            'requested' => $requestedQty,
                        ]),
                    ]);
                }

                // Snapshots: always trust server-side current product price & name
                $unitPrice = (float) $product->price;
                $itemSubtotal = round($unitPrice * $requestedQty, 2);
                $orderSubtotal += $itemSubtotal;

                // Decrement stock immediately inside the lock
                $product->decrement('stock', $requestedQty);

                $orderItemsToCreate[] = [
                    'product_id'      => $product->id,
                    'product_name'    => $product->name,
                    'unit_price'      => $unitPrice,
                    'quantity'        => $requestedQty,
                    'subtotal'        => $itemSubtotal,
                    'discount_amount' => 0.00,
                ];
            }

            // 3. Discount Code Validation & Calculation (Locked)
            $discountRecord = null;
            $totalDiscountAmount = 0.00;

            if (!empty($orderData->discount_code)) {
                /** @var Discount|null $discount */
                $discount = Discount::where('code', $orderData->discount_code)
                    ->lockForUpdate()
                    ->first();

                if (!$discount || !$discount->isValid()) {
                    throw ValidationException::withMessages([
                        'discount_code' => __('order.errors.discount_invalid'),
                    ]);
                }

                $totalDiscountAmount = $discount->calculateDiscountAmount($orderSubtotal);
                $discount->increment('used_count');

                $discountRecord = [
                    'discount_id'    => $discount->id,
                    'code_used'      => $discount->code,
                    'amount_applied' => $totalDiscountAmount,
                    'type'           => $discount->type,
                    'value'          => $discount->value,
                ];
            }

            // 4. Generate unique order number INSIDE the transaction with lock
            $orderNumber = $this->generateOrderNumber();

            // 5. Calculate total amount (created in 'pending' state, payments out of scope)
            $finalTotalAmount = max(0.00, round($orderSubtotal - $totalDiscountAmount, 2));

            $orderAttributes = [
                'order_number'  => $orderNumber,
                'status'        => 'pending',
                'total_amount'  => $finalTotalAmount,
                'staff_id'      => $orderData->staff_id ?? auth()->id(),
                'customer_name' => $orderData->customer_name,
                'notes'         => $orderData->notes,
            ];

            // 6. Delegate persistence to repository
            return $this->orderRepository->createWithItems(
                $orderAttributes,
                $orderItemsToCreate,
                $discountRecord
            );
        });

        return redirect()->back(fallback: route('dashboard'))->with([
            'success'     => __('order.created', ['order_number' => $order->order_number]),
            'order'       => $order,
            'orderNumber' => $order->order_number,
        ]);
    }

    /**
     * Delete an order with guard against deleting completed/paid orders.
     * Restores inventory for cancelled/pending orders before fulfillment.
     */
    public function destroy(int $id): RedirectResponse
    {
        return DB::transaction(function () use ($id) {
            /** @var Order $order */
            $order = Order::with(['items', 'payments'])->lockForUpdate()->findOrFail($id);

            // Cannot delete completed orders or orders with payments
            if ($order->status === 'completed' || $order->payments()->exists()) {
                throw ValidationException::withMessages([
                    'order' => __('order.errors.cannot_delete_paid'),
                ]);
            }

            // If order was pending/preparing (before fulfillment), restore inventory
            if (in_array($order->status, ['pending', 'preparing'], true)) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $this->orderRepository->delete($order);

            return redirect()->route("{$this->routeName()}.index")->with(
                'success',
                __('order.deleted')
            );
        });
    }

    /**
     * Generate sequential daily order number inside transaction lock.
     */
    protected function generateOrderNumber(): string
    {
        $todayPrefix = 'ORD-' . now()->format('Ymd') . '-';

        $lastOrder = Order::where('order_number', 'like', $todayPrefix . '%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder->order_number, -4);
            $newSequence = str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }

        return $todayPrefix . $newSequence;
    }
}
