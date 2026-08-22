<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getMenuProducts(bool $showOutOfStock = true, ?string $category = null, ?string $search = null): Collection
    {
        $query = Product::query();

        if (!$showOutOfStock) {
            $query->where('stock', '>', 0);
        }

        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return $query->orderByDesc('is_featured')
                     ->orderBy('name')
                     ->get();
    }

    public function getCategories(): array
    {
        return Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }
}
