<?php

namespace App\Data;

use App\Models\Order;

/**
 * OrderData serves as the general Data class, defaulting to CreateOrderData for inputs,
 * and providing a bridge to OrderResponseData for outputs.
 */
class OrderData extends CreateOrderData
{
    public static function fromModel(Order $order): OrderResponseData
    {
        return OrderResponseData::fromModel($order);
    }
}
