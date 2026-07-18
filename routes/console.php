<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
// routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('sensors:fetch-readings')
    ->everyFiveMinutes()
    ->appendOutputTo(storage_path('logs/sensor-schedule.log'));
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
