<?php

namespace App\DTOs;

final class CreateOrderData
{
    /**
     * @param OrderItemData[] $items
     */
    public function __construct(
        public readonly array   $items,
        public readonly ?int    $staffId = null,
        public readonly ?string $customerName = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn(array $item) => OrderItemData::fromArray($item),
            $data['items'] ?? []
        );

        return new self(
            items:        $items,
            staffId:      isset($data['staff_id']) ? (int) $data['staff_id'] : null,
            customerName: $data['customer_name'] ?? null,
            notes:        $data['notes'] ?? null,
        );
    }
}
