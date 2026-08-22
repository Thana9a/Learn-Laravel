<?php

namespace App\Data;

use App\Models\OrderItem;
use Spatie\LaravelData\Data;

class OrderItemResponseData extends Data
{
    public function __construct(
        public int $id,
        public int $product_id,
        public string $product_name,
        public float $unit_price,
        public int $quantity,
        public float $subtotal,
        public float $discount_amount = 0.0,
    ) {}

    public static function fromModel(OrderItem $item): self
    {
        return new self(
            id: $item->id,
            product_id: $item->product_id,
            product_name: $item->product_name,
            unit_price: (float) $item->unit_price,
            quantity: $item->quantity,
            subtotal: (float) $item->subtotal,
            discount_amount: (float) ($item->discount_amount ?? 0),
        );
    }
}
