<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateReportRequest;
use App\Models\Report;
use App\Repositories\ReportRepositoryInterface;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * ReportController — HTTP entry point for the Reports feature.
 *
 * Wires HTTP requests to the Service layer and returns Inertia responses.
 * Stays intentionally thin — all business logic lives in ReportService.
 */
class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService             $reportService,
        private readonly ReportRepositoryInterface $reportRepository,
    ) {}

    // ─── Index ─────────────────────────────────────────────────────────────────

    /**
     * Display the Reports dashboard (rendered by Vue via Inertia).
     */
    public function index(Request $request): Response
    {
        $userId  = $request->user()->id;
        $reports = $this->reportRepository->all($userId);
        $stats   = $this->reportRepository->stats($userId);

        return Inertia::render('Reports/Index', [
            'reports' => $reports->map(fn(Report $r) => [
                'id'           => $r->id,
                'title'        => $r->title,
                'type'         => $r->type,
                'status'       => $r->status,
                'generated_at' => $r->generated_at?->diffForHumans(),
                'created_at'   => $r->created_at->diffForHumans(),
                'result'       => $r->result,
            ]),
            'stats'   => $stats,
        ]);
    }

    // ─── Store ─────────────────────────────────────────────────────────────────

    /**
     * Handle the "Generate Report" form submission.
     * Creates a pending report and dispatches the job.
     */
    public function store(GenerateReportRequest $request): RedirectResponse
    {
        $reportData = $request->toDTO();

        $this->reportService->generate(
            userId: $request->user()->id,
            title:  $reportData->title,
            type:   $reportData->type,
        );

        return redirect()->route('reports.index')
                         ->with('success', __('report.queued'));
    }

    // ─── Show ──────────────────────────────────────────────────────────────────

    /**
     * Return a single report's details (used by the Vue modal/detail view).
     */
    public function show(Request $request, Report $report): Response
    {
        // Ensure users can only see their own reports
        abort_unless($report->user_id === $request->user()->id, 403);

        return Inertia::render('Reports/Show', [
            'report' => $report,
        ]);
    }
}
