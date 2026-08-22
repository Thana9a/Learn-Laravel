<?php

namespace App\Repositories;

use App\DTOs\CreateOrderData;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Create an order inside a DB transaction with product lock, snapshotting, and stock decrement.
     *
     * @throws ValidationException
     */
    public function createOrder(CreateOrderData $data): Order
    {
        return DB::transaction(function () use ($data) {
            // 1. Group items by product_id to sum quantities if repeated
            $itemMap = [];
            foreach ($data->items as $item) {
                $itemMap[$item->productId] = ($itemMap[$item->productId] ?? 0) + $item->quantity;
            }

            $productIds = array_keys($itemMap);
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 2. Validate stock and build order items
            $orderItemsToCreate = [];
            $totalAmount = 0.00;

            foreach ($itemMap as $productId => $requestedQty) {
                /** @var Product|null $product */
                $product = $products->get($productId);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "Product ID #{$productId} could not be found.",
                    ]);
                }

                if ($product->stock < $requestedQty) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for \"{$product->name}\". Available: {$product->stock}, requested: {$requestedQty}.",
                    ]);
                }

                $unitPrice = (float) $product->price;
                $subtotal = round($unitPrice * $requestedQty, 2);
                $totalAmount += $subtotal;

                // Decrement stock
                $product->decrement('stock', $requestedQty);

                $orderItemsToCreate[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $unitPrice,
                    'quantity'     => $requestedQty,
                    'subtotal'     => $subtotal,
                ];
            }

            // 3. Generate unique order number (e.g. ORD-YYYYMMDD-XXXX)
            $orderNumber = $this->generateOrderNumber();

            // 4. Create Order record
            $order = Order::create([
                'order_number'  => $orderNumber,
                'status'        => 'pending',
                'total_amount'  => $totalAmount,
                'staff_id'      => $data->staffId,
                'customer_name' => $data->customerName,
                'notes'         => $data->notes,
            ]);

            // 5. Create OrderItems
            foreach ($orderItemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            return $order->load(['items', 'staff']);
        });
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return Order::with(['items', 'staff'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items', 'staff'])->find($id);
    }

    /**
     * Generate an order number like ORD-20260816-0001
     */
    protected function generateOrderNumber(): string
    {
        $todayPrefix = 'ORD-' . now()->format('Ymd') . '-';
        $lastOrder = Order::where('order_number', 'like', $todayPrefix . '%')
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
