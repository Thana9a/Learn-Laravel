<?php

namespace App\DTOs;

use App\Models\Product;

final class ProductData
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $category,
        public readonly float   $price,
        public readonly int     $stock,
        public readonly ?string $description = null,
        public readonly ?string $imageUrl = null,
        public readonly bool    $isFeatured = false,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id:          $product->id,
            name:        $product->name,
            category:    $product->category,
            price:       (float) $product->price,
            stock:       (int) $product->stock,
            description: $product->description,
            imageUrl:    $product->image_url,
            isFeatured:  (bool) $product->is_featured,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'category'    => $this->category,
            'price'       => $this->price,
            'stock'       => $this->stock,
            'description' => $this->description,
            'image_url'   => $this->imageUrl,
            'is_featured' => $this->isFeatured,
        ];
    }
}
