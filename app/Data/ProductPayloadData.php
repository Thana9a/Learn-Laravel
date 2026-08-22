<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ProductPayloadData extends Data
{
    public function __construct(
        public string $name,
        public string $category,
        public float $price,
        public int $stock,
        public ?string $description,
        public ?string $image_url,
        public bool $is_featured,
    ) {}
}
