<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\DTOs\ProductData;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PosOrderController extends Controller
{
    public function __construct(
        protected readonly ProductRepositoryInterface $productRepository,
        protected readonly OrderRepositoryInterface   $orderRepository,
    ) {}

    /**
     * Display the Barista POS Order Screen.
     */
    public function index(Request $request): Response
    {
        $products = $this->productRepository->getMenuProducts();
        $categories = $this->productRepository->getCategories();
        $staffUsers = User::select('id', 'name', 'email')->orderBy('name')->get();
        $recentOrders = $this->orderRepository->getRecentOrders(5);

        return Inertia::render('Pos/OrderScreen', [
            'products'     => $products->map(fn($p) => ProductData::fromModel($p)->toArray())->values(),
            'categories'   => array_values($categories),
            'staffUsers'   => $staffUsers,
            'recentOrders' => $recentOrders->map(fn(Order $o) => OrderData::fromModel($o)->toArray())->values(),
            'activeStaff'  => $request->user() ? [
                'id'    => $request->user()->id,
                'name'  => $request->user()->name,
                'email' => $request->user()->email,
            ] : null,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(CreateOrderRequest $request): RedirectResponse
    {
        $dto = $request->toDTO();
        $order = $this->orderRepository->createOrder($dto);

        return redirect()->route('pos.index')->with([
            'success'     => "Order #{$order->order_number} submitted successfully!",
            'order'       => OrderData::fromModel($order)->toArray(),
            'orderNumber' => $order->order_number,
        ]);
    }
}
