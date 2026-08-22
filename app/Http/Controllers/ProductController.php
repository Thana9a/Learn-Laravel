<?php

namespace App\Http\Controllers;

use App\DTOs\ProductData;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of products with stats and filters.
     */
    public function index(Request $request): Response
    {
        $query = Product::query();
        $filters = [
            'search' => $request->string('search')->toString(),
            'category' => $request->string('category')->toString() ?: 'All',
        ];

        if ($filters['search'] !== '') {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['category'] !== 'All') {
            $query->where('category', $filters['category']);
        }

        $products = $query->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        $stats = [
            'total_products' => Product::count(),
            'avg_price' => round(Product::avg('price') ?? 0, 2),
            'low_stock_count' => Product::where('stock', '<', 10)->count(),
            'total_inventory' => Product::sum('stock'),
        ];

        $categories = ['Coffee', 'Tea & Beverages', 'Pastries', 'Coffee Beans'];

        return Inertia::render('Products/Index', [
            'products' => $products
                ->map(fn (Product $product) => ProductData::fromModel($product)->toArray())
                ->values(),
            'stats' => $stats,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->toDTO()->toArray());

        return redirect()->route('products.index')->with('success', __('product.created'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->toDTO()->toArray());

        return redirect()->route('products.index')->with('success', __('product.updated'));
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', __('product.deleted'));
    }
}
