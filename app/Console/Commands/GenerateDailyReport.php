<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;
use App\Models\User;

/**
 * GenerateDailyReport Artisan Command
 *
 * Usage:
 *   php artisan report:daily
 *   php artisan report:daily --user=1
 *
 * This command is registered in the Scheduler (routes/console.php)
 * to run automatically every day at midnight.
 */
class GenerateDailyReport extends Command
{
    protected $signature   = 'report:daily {--user= : User ID to generate report for (defaults to first admin)}';
    protected $description = 'Generate the daily sales report and dispatch it to the queue';

    public function __construct(
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    // ─── Handle ────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $userId = $this->option('user')
            ? (int) $this->option('user')
            : $this->resolveDefaultUserId();

        if (! $userId) {
            $this->error('No users found in the database. Please seed some users first.');
            return self::FAILURE;
        }

        $this->info("🚀 Dispatching daily report for user #{$userId}...");

        $report = $this->reportService->generate(
            userId: $userId,
            title:  'Daily Sales Report — ' . now()->format('d M Y'),
            type:   'sales',
        );

        $this->info("✅ Report #{$report->id} queued successfully (status: {$report->status}).");
        $this->line("   Run <comment>php artisan queue:work --once</comment> to process it.");

        return self::SUCCESS;
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    private function resolveDefaultUserId(): ?int
    {
        return User::first()?->id;
    }
}
