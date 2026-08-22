<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Display order history list.
     */
    public function index(Request $request): Response
    {
        $query = Order::with(['items', 'staff']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_revenue' => round((float) Order::sum('total_amount'), 2),
        ];

        return Inertia::render('Orders/Index', [
            'orders' => [
                'data' => $orders->getCollection()->map(fn (Order $o) => OrderData::fromModel($o)->toArray()),
                'links' => $orders->linkCollection(),
                'total' => $orders->total(),
            ],
            'filters' => [
                'status' => $request->status ?? 'all',
                'search' => $request->search ?? '',
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Display a single order receipt / detail view.
     */
    public function show(Order $order): Response
    {
        $order->load(['items', 'staff']);

        return Inertia::render('Orders/Show', [
            'order' => OrderData::fromModel($order)->toArray(),
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(CreateOrderRequest $request): RedirectResponse
    {
        $dto = $request->toDTO();
        $order = $this->orderRepository->createOrder($dto);

        return redirect()->route('dashboard')->with([
            'success' => "Order #{$order->order_number} submitted successfully!",
            'order' => OrderData::fromModel($order)->toArray(),
            'orderNumber' => $order->order_number,
        ]);
    }
}
