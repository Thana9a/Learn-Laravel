<?php

namespace App\Http\Controllers;

use App\Data\OrderResponseData;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Display the Admin Dashboard.
     */
    public function index(Request $request): Response
    {
        $recentOrders = $this->orderRepository->getRecentOrders(5);
        $totalOrders = Order::whereDate('created_at', today())->count();
        $totalSales = Order::whereDate('created_at', today())->whereIn('status', ['paid', 'completed'])->sum('total_amount');
        $activeProducts = Product::where('stock', '>', 0)->count();

        return Inertia::render('Dashboard', [
            'recentOrders' => $recentOrders->map(fn (Order $o) => OrderResponseData::fromModel($o)->toArray())->values(),
            'stats' => [
                [
                    'label' => __('dashboard.stats.today_sales', [], 'en'), 
                    'value' => '$' . number_format((float) $totalSales, 2),
                    'trend' => '+0%', 
                    'icon' => 'TrendingUp'
                ],
                [
                    'label' => __('dashboard.stats.orders_today', [], 'en'), 
                    'value' => (string) $totalOrders,
                    'trend' => '+0%', 
                    'icon' => 'Receipt'
                ],
                [
                    'label' => __('dashboard.stats.active_products', [], 'en'), 
                    'value' => (string) $activeProducts,
                    'icon' => 'Package'
                ],
            ]
        ]);
    }
}
