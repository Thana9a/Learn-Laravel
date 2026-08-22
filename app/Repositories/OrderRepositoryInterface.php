<?php

namespace App\Repositories;

use App\DTOs\CreateOrderData;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function createOrder(CreateOrderData $data): Order;
    public function getRecentOrders(int $limit = 10): Collection;
    public function findById(int $id): ?Order;
}
