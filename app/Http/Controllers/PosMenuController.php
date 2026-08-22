<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\DTOs\ProductData;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PosMenuController extends Controller
{
    public function __construct(
        protected readonly ProductRepositoryInterface $productRepository,
        protected readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Display the Barista Order Screen (Dashboard).
     */
    public function index(Request $request): Response
    {
        $products = $this->productRepository->getMenuProducts();
        $categories = $this->productRepository->getCategories();
        $staffUsers = User::select('id', 'name', 'email')->orderBy('name')->get();
        $recentOrders = $this->orderRepository->getRecentOrders(5);

        return Inertia::render('Pos/Menu/Index', [
            'products' => $products->map(fn ($p) => ProductData::fromModel($p)->toArray())->values(),
            'categories' => array_values($categories),
            'staffUsers' => $staffUsers,
            'recentOrders' => $recentOrders->map(fn (Order $o) => OrderData::fromModel($o)->toArray())->values(),
            'activeStaff' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ] : null,
        ]);
    }
}
