<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Models\Product;
use App\Repositories\ReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * ReportService — orchestrates the report generation workflow.
 *
 * Responsibilities:
 *  1. Validate & resolve what data is needed (via DTO)
 *  2. Persist the Report record (via Repository)
 *  3. Dispatch the Job to the Queue for async processing
 */
class ReportService
{
    public function __construct(
        private readonly ReportRepositoryInterface $reportRepository,
    ) {}

    // ─── Public API ────────────────────────────────────────────────────────────

    /**
     * Kick off a new report generation.
     * Returns the freshly created Report (in 'pending' state).
     */
    public function generate(int $userId, string $title = 'Sales Report', string $type = 'sales'): Report
    {
        // 1. Persist a pending record first so the user sees immediate feedback
        $report = $this->reportRepository->create([
            'title'   => $title,
            'type'    => $type,
            'status'  => 'pending',
            'user_id' => $userId,
        ]);

        // 2. Build the DTO from current product/sales data (snapshot at dispatch time)
        $dto = $this->buildDTO($title, $type);

        // 3. Dispatch job to the queue — the heavy work happens asynchronously
        GenerateReportJob::dispatch($report, $dto);

        return $report;
    }

    // ─── Internal Helpers ──────────────────────────────────────────────────────

    /**
     * Aggregate current data into a DTO for the job to process.
     * In a real app this might query an analytics DB, external API, etc.
     */
    private function buildDTO(string $title, string $type): ReportDTO
    {
        // Aggregate sales data from Products table
        $products  = Product::all();
        $totalSales = $products->sum(fn($p) => $p->price * $p->stock);
        $avgPrice   = $products->avg('price') ?? 0;

        // Group by category for top categories chart
        $topCategories = $products
            ->groupBy('category')
            ->map(fn($group, $cat) => [
                'category' => $cat,
                'count'    => $group->count(),
                'revenue'  => round($group->sum(fn($p) => $p->price * $p->stock), 2),
            ])
            ->values()
            ->toArray();

        return ReportDTO::fromArray([
            'title'               => $title,
            'type'                => $type,
            'total_sales'         => round($totalSales, 2),
            'total_orders'        => $products->count(),   // treating each product as an "order line"
            'average_order_value' => round($avgPrice, 2),
            'total_products'      => $products->count(),
            'top_categories'      => $topCategories,
            'generated_at'        => Carbon::now()->toIso8601String(),
        ]);
    }
}
