<?php

namespace App\Console\Commands;

use App\Services\HealthAnalyticsService;
use Illuminate\Console\Command;

class GenerateHealthAnalyticsSnapshot extends Command
{
    protected $signature = 'health:analytics-snapshot {--period=30 : Reporting period in days (30, 90, 180, or 365)}';

    protected $description = 'Generate a privacy-safe health analytics snapshot for administrators';

    public function handle(HealthAnalyticsService $analytics): int
    {
        $period = (int) $this->option('period');
        if (! in_array($period, HealthAnalyticsService::ALLOWED_PERIODS, true)) {
            $this->error('Period must be one of: '.implode(', ', HealthAnalyticsService::ALLOWED_PERIODS));
            return self::FAILURE;
        }

        $snapshot = $analytics->createSnapshot($period, 'scheduler');
        $this->info('Health analytics snapshot #'.$snapshot->id.' generated for '.$period.' days.');

        return self::SUCCESS;
    }
}
