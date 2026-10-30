<?php

namespace App\Http\Middleware;

use App\Services\ApelScheduleAutomationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoGenerateApelScheduleMiddleware
{
    /**
     * Handle an incoming request.
     * Otomatis memastikan jadwal apel hari Senin dan Jumat selalu tersedia.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jalankan hanya untuk request GET agar tidak membebani operasi mutasi/POST
        if ($request->isMethod('GET')) {
            try {
                ApelScheduleAutomationService::ensureMondayAndFridaySchedules();
            } catch (\Throwable $e) {
                // Jangan gagalkan request user jika terjadi kendala pada database
                report($e);
            }
        }

        return $next($request);
    }
}
