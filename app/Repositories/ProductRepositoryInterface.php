<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getMenuProducts(bool $showOutOfStock = true, ?string $category = null, ?string $search = null): Collection;
    public function getCategories(): array;
    public function findById(int $id): ?Product;
}
