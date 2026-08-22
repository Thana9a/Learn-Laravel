<?php

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object for a generated report.
 *
 * Shapes raw DB/aggregation data into a clean, typed structure
 * that flows between the Repository → Service → Job layers.
 */
final class ReportDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $type,
        public readonly float  $totalSales,
        public readonly int    $totalOrders,
        public readonly float  $averageOrderValue,
        public readonly int    $totalProducts,
        public readonly array  $topCategories,
        public readonly Carbon $generatedAt,
    ) {}

    // ─── Factory ───────────────────────────────────────────────────────────────

    /**
     * Build a DTO from a plain associative array (e.g. from DB aggregation).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title:             $data['title']               ?? 'Sales Report',
            type:              $data['type']                ?? 'sales',
            totalSales:        (float)  ($data['total_sales']         ?? 0),
            totalOrders:       (int)    ($data['total_orders']        ?? 0),
            averageOrderValue: (float)  ($data['average_order_value'] ?? 0),
            totalProducts:     (int)    ($data['total_products']      ?? 0),
            topCategories:     (array)  ($data['top_categories']      ?? []),
            generatedAt:       isset($data['generated_at'])
                                   ? Carbon::parse($data['generated_at'])
                                   : Carbon::now(),
        );
    }

    // ─── Serialization ─────────────────────────────────────────────────────────

    /**
     * Convert to plain array — stored in the reports.result JSON column.
     */
    public function toArray(): array
    {
        return [
            'title'               => $this->title,
            'type'                => $this->type,
            'total_sales'         => $this->totalSales,
            'total_orders'        => $this->totalOrders,
            'average_order_value' => $this->averageOrderValue,
            'total_products'      => $this->totalProducts,
            'top_categories'      => $this->topCategories,
            'generated_at'        => $this->generatedAt->toIso8601String(),
        ];
    }
}
