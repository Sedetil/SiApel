<?php

use App\Http\Middleware\AutoGenerateApelScheduleMiddleware;
use App\Http\Middleware\EnsureUserHasRole;
use App\Services\ApelScheduleAutomationService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Otomatis generate jadwal Senin & Jumat di background setiap web request
        $middleware->web(append: [
            AutoGenerateApelScheduleMiddleware::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Task Scheduler berjalan otomatis setiap hari Minggu & Kamis tengah malam
        $schedule->call(function () {
            ApelScheduleAutomationService::ensureMondayAndFridaySchedules();
        })->dailyAt('00:01')->name('auto-generate-apel-schedules');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
