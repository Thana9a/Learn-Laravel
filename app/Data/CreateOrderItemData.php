<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CreateOrderItemData extends Data
{
    public function __construct(
        #[Required, Min(1)]
        public int $product_id,

        #[Required, Min(1)]
        public int $quantity = 1,

        public ?string $discount_code = null,
    ) {}
}
