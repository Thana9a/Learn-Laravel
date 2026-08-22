<?php

namespace App\Listeners;

use App\Events\ReportGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * HandleReportGenerated Listener
 *
 * Reacts to the ReportGenerated event after the job fires it.
 * Implements ShouldQueue so the listener itself runs asynchronously.
 *
 * Responsibilities:
 *  - Log the completion for audit/debugging
 *  - Could also: send email notification, update analytics, trigger webhooks
 */
class HandleReportGenerated implements ShouldQueue
{
    public function __construct() {}

    // ─── Handle ────────────────────────────────────────────────────────────────

    public function handle(ReportGenerated $event): void
    {
        $report = $event->report;

        Log::info('Report generated successfully', [
            'report_id'    => $report->id,
            'title'        => $report->title,
            'user_id'      => $report->user_id,
            'generated_at' => $report->generated_at?->toDateTimeString(),
            'total_sales'  => $report->result['total_sales'] ?? 0,
        ]);

        // ── Extension points ────────────────────────────────────────────────────
        // In production you'd do things like:
        //
        // Mail::to($report->user)->send(new ReportReadyMail($report));
        // Notification::send($report->user, new ReportReadyNotification($report));
        // Http::post(config('webhooks.reports'), $report->result);
    }

    // ─── Error Handling ────────────────────────────────────────────────────────

    public function failed(ReportGenerated $event, \Throwable $exception): void
    {
        Log::error('HandleReportGenerated listener failed', [
            'report_id' => $event->report->id,
            'error'     => $exception->getMessage(),
        ]);
    }
}
