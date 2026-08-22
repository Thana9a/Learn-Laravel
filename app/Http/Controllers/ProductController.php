<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of products with stats and filters.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('description', 'ilike', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        // Fetch products
        $products = $query->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        // Computed Stats for the Dashboard
        $stats = [
            'total_products' => Product::count(),
            'avg_price' => round(Product::avg('price') ?? 0, 2),
            'low_stock_count' => Product::where('stock', '<', 10)->count(),
            'total_inventory' => Product::sum('stock'),
        ];

        $categories = ['Coffee', 'Tea & Beverages', 'Pastries', 'Coffee Beans'];

        return view('products.index', compact('products', 'stats', 'categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Coffee,Tea & Beverages,Pastries,Coffee Beans',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_featured' => 'sometimes|boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Coffee,Tea & Beverages,Pastries,Coffee Beans',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_featured' => 'sometimes|boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
