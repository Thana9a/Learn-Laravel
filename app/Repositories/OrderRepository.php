<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function model(): string
    {
        return Order::class;
    }

    public function searchableFields(): array
    {
        return ['order_number', 'customer_name'];
    }

    public function sortableFields(): array
    {
        return ['id', 'order_number', 'total_amount', 'created_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['staff_id'])) {
            $query->where('staff_id', $filters['staff_id']);
        }
    }

    /**
     * Persist an order, its items, and optional order discount.
     * Note: Transaction handling belongs in the calling service/controller.
     */
    public function createWithItems(array $orderData, array $items, ?array $discountRecord = null): Order
    {
        /** @var Order $order */
        $order = Order::create($orderData);

        foreach ($items as $item) {
            $order->items()->create([
                'product_id'      => $item['product_id'],
                'product_name'    => $item['product_name'],
                'unit_price'      => $item['unit_price'],
                'quantity'        => $item['quantity'],
                'subtotal'        => $item['subtotal'],
                'discount_amount' => $item['discount_amount'] ?? 0.00,
            ]);
        }

        if ($discountRecord) {
            $order->orderDiscounts()->create($discountRecord);
        }

        return $order->load(['items', 'staff', 'orderDiscounts']);
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return Order::with(['items', 'staff', 'orderDiscounts'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items', 'staff', 'orderDiscounts'])->find($id);
    }
}
