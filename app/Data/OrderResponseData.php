<?php

namespace App\Data;

use App\Models\Order;
use App\Models\OrderItem;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class OrderResponseData extends Data
{
    /**
     * @param OrderItemResponseData[] $items
     */
    public function __construct(
        public int $id,
        public string $order_number,
        public string $status,
        public float $total_amount,
        #[DataCollectionOf(OrderItemResponseData::class)]
        public array $items = [],
        public ?int $staff_id = null,
        public ?string $staff_name = null,
        public ?string $customer_name = null,
        public ?string $notes = null,
        public ?string $created_at = null,
    ) {}

    public static function fromModel(Order $order): self
    {
        $items = $order->relationLoaded('items') || $order->items !== null
            ? $order->items->map(fn (OrderItem $i) => OrderItemResponseData::fromModel($i))->values()->all()
            : [];

        return new self(
            id: $order->id,
            order_number: $order->order_number,
            status: $order->status,
            total_amount: (float) $order->total_amount,
            items: $items,
            staff_id: $order->staff_id,
            staff_name: $order->staff?->name,
            customer_name: $order->customer_name,
            notes: $order->notes,
            created_at: $order->created_at?->toIso8601String(),
        );
    }
}
