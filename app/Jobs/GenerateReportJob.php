<?php

namespace App\Jobs;

use App\DTOs\ReportDTO;
use App\Events\ReportGenerated;
use App\Models\Report;
use App\Repositories\ReportRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * GenerateReportJob — the async worker.
 *
 * Dispatched by ReportService onto the 'default' queue.
 * Simulates heavy computation (could be: PDF generation, DB aggregation, API calls).
 * On success  → fires ReportGenerated event.
 * On failure  → marks report as 'failed' in the DB.
 */
class GenerateReportJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 5;

    public function __construct(
        public readonly Report    $report,
        public readonly ReportDTO $dto,
    ) {}

    // ─── Handle ────────────────────────────────────────────────────────────────

    public function handle(ReportRepositoryInterface $repository): void
    {
        // 1. Mark as processing
        $repository->updateStatus($this->report->id, 'processing');

        // 2. Simulate heavy computation (remove in production)
        sleep(2);

        // 3. Persist the result
        $updatedReport = $repository->updateStatus(
            id:     $this->report->id,
            status: 'done',
            result: $this->dto->toArray(),
        );

        // 4. Fire the event — Listener + Reverb broadcasting picks this up
        ReportGenerated::dispatch($updatedReport);
    }

    // ─── Failure ───────────────────────────────────────────────────────────────

    public function failed(Throwable $exception): void
    {
        // Resolve repository from container even in failure callback
        $repository = app(ReportRepositoryInterface::class);

        $repository->updateStatus(
            id:           $this->report->id,
            status:       'failed',
            errorMessage: $exception->getMessage(),
        );
    }
}
