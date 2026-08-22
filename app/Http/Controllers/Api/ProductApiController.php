<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(
        protected readonly ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Search products for autocomplete and POS live lookups.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->query('query') ?? $request->query('search');
        $category = $request->query('category');
        $inStockOnly = $request->boolean('in_stock_only', false);

        $products = $this->productRepository->getMenuProducts(
            showOutOfStock: !$inStockOnly,
            category: $category,
            search: $search
        );

        return response()->json([
            'data' => $products->map(fn (Product $product) => [
                'id'          => $product->id,
                'name'        => $product->name,
                'category'    => $product->category,
                'price'       => (float) $product->price,
                'stock'       => $product->stock,
                'description' => $product->description,
                'image_url'   => $product->image_url,
                'is_featured' => (bool) $product->is_featured,
            ]),
        ]);
    }
}
