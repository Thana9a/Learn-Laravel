<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CreateOrderData extends Data
{
    /**
     * @param CreateOrderItemData[] $items
     */
    public function __construct(
        #[Required, ArrayType, Min(1), DataCollectionOf(CreateOrderItemData::class)]
        public array $items,

        public ?int $staff_id = null,
        public ?string $customer_name = null,
        public ?string $notes = null,
        public ?string $discount_code = null,
    ) {}
}
