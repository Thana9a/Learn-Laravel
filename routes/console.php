<?php

use App\Console\Commands\GenerateDailyReport;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| The GenerateDailyReport command is scheduled to run every day at midnight.
| It will dispatch a GenerateReportJob onto the queue for each user.
|
| Run locally: php artisan schedule:run
| Run worker:  php artisan queue:work
|
*/

Schedule::command(GenerateDailyReport::class)
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/report-schedule.log'))
    ->description('Generate daily sales report at midnight');
