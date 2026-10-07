<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('health:analytics-snapshot --period=30')
    ->dailyAt('01:00')
    ->withoutOverlapping();

Schedule::command('health:analytics-snapshot --period=90')
    ->weeklyOn(1, '01:15')
    ->withoutOverlapping();
