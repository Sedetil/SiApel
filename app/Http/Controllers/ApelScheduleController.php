<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApelScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = ApelSchedule::withCount('attendanceRecords');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category') && in_array($request->input('category'), ['rutin', 'tambahan', 'khusus'])) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('date')) {
            $query->whereDate('schedule_date', $request->input('date'));
        }

        $schedules = $query->orderBy('schedule_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(10)
            ->withQueryString();

        $categoryCounts = [
            'all' => ApelSchedule::count(),
            'rutin' => ApelSchedule::where('category', 'rutin')->count(),
            'tambahan' => ApelSchedule::where('category', 'tambahan')->count(),
            'khusus' => ApelSchedule::where('category', 'khusus')->count(),
        ];

        return view('pages.schedules.index', compact('schedules', 'categoryCounts'));
    }

    public function create(): View
    {
        return view('pages.schedules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:rutin,tambahan,khusus',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'attendance_start' => 'required|date_format:H:i|before_or_equal:start_time',
            'attendance_end' => 'required|date_format:H:i|after:attendance_start',
            'late_threshold_time' => 'required|date_format:H:i|after_or_equal:attendance_start',
            'location' => 'required|string|max:255',
            'status' => 'required|in:scheduled,active,completed,cancelled',
            'description' => 'nullable|string',
        ], [
            'end_time.after' => 'Waktu apel selesai harus lebih lambat dari waktu mulai.',
            'attendance_start.before_or_equal' => 'Waktu buka presensi harus sebelum atau bersamaan dengan jam mulai apel.',
            'attendance_end.after' => 'Waktu tutup presensi harus lebih lambat dari waktu buka presensi.',
            'late_threshold_time.after_or_equal' => 'Batas keterlambatan harus setelah atau sama dengan waktu buka presensi.',
        ]);

        $schedule = ApelSchedule::create(array_merge($validated, [
            'created_by' => Auth::id(),
        ]));

        AuditLog::log(
            action: 'CREATE_SCHEDULE',
            description: "Membuat jadwal apel: {$schedule->title} pada {$schedule->schedule_date->format('d/m/Y')}.",
            entityType: 'ApelSchedule',
            entityId: $schedule->id
        );

        return redirect()->route('admin.schedules.index')->with('status', 'Jadwal apel berhasil dibuat.');
    }

    public function edit(ApelSchedule $schedule): View
    {
        return view('pages.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, ApelSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:rutin,tambahan,khusus',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'attendance_start' => 'required|date_format:H:i|before_or_equal:start_time',
            'attendance_end' => 'required|date_format:H:i|after:attendance_start',
            'late_threshold_time' => 'required|date_format:H:i|after_or_equal:attendance_start',
            'location' => 'required|string|max:255',
            'status' => 'required|in:scheduled,active,completed,cancelled',
            'description' => 'nullable|string',
        ]);

        $schedule->update($validated);

        AuditLog::log(
            action: 'UPDATE_SCHEDULE',
            description: "Memperbarui jadwal apel: {$schedule->title}.",
            entityType: 'ApelSchedule',
            entityId: $schedule->id
        );

        return redirect()->route('admin.schedules.index')->with('status', 'Jadwal apel berhasil diperbarui.');
    }

    public function cancel(Request $request, ApelSchedule $schedule): RedirectResponse
    {
        $reason = $request->input('reason', 'Dibatalkan oleh Administrator');

        $schedule->batalkanApel($reason);

        AuditLog::log(
            action: 'CANCEL_SCHEDULE',
            description: "Membatalkan jadwal apel: {$schedule->title}. Alasan: {$reason}.",
            entityType: 'ApelSchedule',
            entityId: $schedule->id
        );

        return back()->with('status', "Jadwal apel '{$schedule->title}' berhasil dibatalkan.");
    }

    public function updateStatus(Request $request, ApelSchedule $schedule): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:scheduled,active,completed,cancelled',
        ]);

        $newStatus = $request->input('status');
        $schedule->update(['status' => $newStatus]);

        AuditLog::log(
            action: 'UPDATE_SCHEDULE_STATUS',
            description: "Mengubah status jadwal apel {$schedule->title} menjadi {$newStatus}.",
            entityType: 'ApelSchedule',
            entityId: $schedule->id
        );

        return back()->with('status', "Status jadwal apel berhasil diubah menjadi: {$newStatus}.");
    }

    /**
     * FR-04: Jadwal yang telah memiliki catatan absensi tidak boleh dihapus permanen
     */
    public function destroy(ApelSchedule $schedule): RedirectResponse
    {
        if ($schedule->attendanceRecords()->exists()) {
            return back()->withErrors([
                'schedule' => 'Jadwal tidak dapat dihapus karena sudah memiliki riwayat catatan absensi. Anda dapat mengubah statusnya menjadi Dibatalkan (Cancelled).',
            ]);
        }

        $title = $schedule->title;
        $schedule->delete();

        AuditLog::log(
            action: 'DELETE_SCHEDULE',
            description: "Menghapus jadwal apel: {$title}.",
            entityType: 'ApelSchedule',
            entityId: $schedule->id
        );

        return redirect()->route('admin.schedules.index')->with('status', 'Jadwal apel berhasil dihapus.');
    }
}
