<?php

use App\Services\ApelScheduleAutomationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('apel:generate-schedules {--weeks=4 : Jumlah minggu ke depan}', function () {
    $weeks = (int) $this->option('weeks');
    $this->info("Men-generate jadwal apel rutin Senin & Jumat untuk {$weeks} minggu ke depan...");
    $schedules = ApelScheduleAutomationService::ensureMondayAndFridaySchedules($weeks);
    $this->info("Berhasil memastikan {$schedules->count()} jadwal apel Senin & Jumat tersedia.");
})->purpose('Generate jadwal apel rutin Senin & Jumat otomatis');
