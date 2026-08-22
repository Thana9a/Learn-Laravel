<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Persist an order, its snapshot items, and optional order discount.
     * Note: Transaction boundaries must be handled by the caller.
     */
    public function createWithItems(array $orderData, array $items, ?array $discountRecord = null): Order;

    public function getRecentOrders(int $limit = 10): Collection;

    public function findById(int $id): ?Order;
}
