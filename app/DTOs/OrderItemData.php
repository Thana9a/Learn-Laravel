<?php

namespace App\DTOs;

use App\Models\OrderItem;

final class OrderItemData
{
    public function __construct(
        public readonly int     $productId,
        public readonly int     $quantity,
        public readonly ?string $productName = null,
        public readonly ?float  $unitPrice = null,
        public readonly ?float  $subtotal = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId:   (int) $data['product_id'],
            quantity:    (int) $data['quantity'],
            productName: $data['product_name'] ?? null,
            unitPrice:   isset($data['unit_price']) ? (float) $data['unit_price'] : null,
            subtotal:    isset($data['subtotal']) ? (float) $data['subtotal'] : null,
        );
    }

    public static function fromModel(OrderItem $item): self
    {
        return new self(
            productId:   $item->product_id,
            quantity:    (int) $item->quantity,
            productName: $item->product_name,
            unitPrice:   (float) $item->unit_price,
            subtotal:    (float) $item->subtotal,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id'   => $this->productId,
            'quantity'     => $this->quantity,
            'product_name' => $this->productName,
            'unit_price'   => $this->unitPrice,
            'subtotal'     => $this->subtotal,
        ];
    }
}
