<?php

namespace App\DTOs;

use App\Models\Order;

final class OrderData
{
    /**
     * @param OrderItemData[] $items
     */
    public function __construct(
        public readonly int     $id,
        public readonly string  $orderNumber,
        public readonly string  $status,
        public readonly float   $totalAmount,
        public readonly array   $items,
        public readonly ?string $staffName = null,
        public readonly ?string $customerName = null,
        public readonly ?string $notes = null,
        public readonly ?string $createdAt = null,
    ) {}

    public static function fromModel(Order $order): self
    {
        $items = $order->items->map(
            fn($item) => OrderItemData::fromModel($item)
        )->all();

        return new self(
            id:           $order->id,
            orderNumber:  $order->order_number,
            status:       $order->status,
            totalAmount:  (float) $order->total_amount,
            items:        $items,
            staffName:    $order->staff?->name,
            customerName: $order->customer_name,
            notes:        $order->notes,
            createdAt:    $order->created_at?->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'order_number'  => $this->orderNumber,
            'status'        => $this->status,
            'total_amount'  => $this->totalAmount,
            'items'         => array_map(fn(OrderItemData $i) => $i->toArray(), $this->items),
            'staff_name'    => $this->staffName,
            'customer_name' => $this->customerName,
            'notes'         => $this->notes,
            'created_at'    => $this->createdAt,
        ];
    }
}
