<?php

namespace App\Repositories;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent-backed implementation of ReportRepositoryInterface.
 * All DB access for the Report model goes through here.
 */
class ReportRepository implements ReportRepositoryInterface
{
    // ─── Read ──────────────────────────────────────────────────────────────────

    /**
     * Fetch all reports for a given user, newest first.
     */
    public function all(int $userId): Collection
    {
        return Report::where('user_id', $userId)
                     ->orderByDesc('created_at')
                     ->get();
    }

    /**
     * Find a single report by ID, or return null.
     */
    public function findById(int $id): ?Report
    {
        return Report::find($id);
    }

    /**
     * Aggregate stats for the dashboard header cards.
     */
    public function stats(int $userId): array
    {
        $base = Report::where('user_id', $userId);

        return [
            'total'      => (clone $base)->count(),
            'done'       => (clone $base)->done()->count(),
            'processing' => (clone $base)->processing()->count(),
            'failed'     => (clone $base)->failed()->count(),
            'pending'    => (clone $base)->pending()->count(),
        ];
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    /**
     * Persist a new report record and return the fresh model.
     */
    public function create(array $attributes): Report
    {
        return Report::create($attributes);
    }

    /**
     * Flip a report's status and optionally store its result payload.
     */
    public function updateStatus(
        int     $id,
        string  $status,
        ?array  $result       = null,
        ?string $errorMessage = null,
    ): Report {
        $report = Report::findOrFail($id);

        $report->update([
            'status'        => $status,
            'result'        => $result,
            'error_message' => $errorMessage,
            'generated_at'  => in_array($status, ['done', 'failed']) ? Carbon::now() : null,
        ]);

        return $report->fresh();
    }
}
