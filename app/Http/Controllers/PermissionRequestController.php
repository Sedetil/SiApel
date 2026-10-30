<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\PermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $type = $request->query('type');
        $search = $request->query('search');

        $query = PermissionRequest::with(['participant', 'schedule', 'decider'])
            ->latest();

        if ($status === 'menunggu') {
            $query->where('status', 'menunggu');
        } elseif ($status === 'riwayat') {
            $query->whereIn('status', ['disetujui', 'ditolak']);
        } elseif (in_array($status, ['disetujui', 'ditolak'])) {
            $query->where('status', $status);
        }

        if ($type && in_array($type, ['izin', 'sakit'])) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->whereHas('participant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => PermissionRequest::count(),
            'menunggu' => PermissionRequest::where('status', 'menunggu')->count(),
            'disetujui' => PermissionRequest::where('status', 'disetujui')->count(),
            'ditolak' => PermissionRequest::where('status', 'ditolak')->count(),
        ];

        $schedules = ApelSchedule::latest('schedule_date')->take(10)->get();

        return view('pages.permissions.index', compact('requests', 'stats', 'schedules', 'status', 'type', 'search'));
    }

    public function show(PermissionRequest $permissionRequest)
    {
        $permissionRequest->load(['participant', 'schedule', 'decider']);

        return response()->json([
            'success' => true,
            'data' => $permissionRequest,
        ]);
    }

    public function decide(Request $request, PermissionRequest $permissionRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        $permissionRequest->update([
            'status' => $validated['status'],
            'decided_by' => $user->id,
            'decided_at' => now(),
            'notes' => $validated['notes'] ?? ($validated['status'] === 'disetujui' ? 'Disetujui oleh '.$user->name : 'Ditolak oleh '.$user->name),
        ]);

        // If approved, reflect in attendance records
        if ($validated['status'] === 'disetujui' && $permissionRequest->apel_schedule_id) {
            AttendanceRecord::updateOrCreate(
                [
                    'apel_schedule_id' => $permissionRequest->apel_schedule_id,
                    'participant_id' => $permissionRequest->participant_id,
                ],
                [
                    'status' => $permissionRequest->type === 'sakit' ? 'sakit' : 'izin',
                    'notes' => 'Disetujui dari pengajuan: '.$permissionRequest->reason,
                    'recorded_by' => $user->id,
                ]
            );
        }

        AuditLog::log(
            action: 'DECIDE_PERMISSION',
            description: "Memutuskan pengajuan {$permissionRequest->type} untuk {$permissionRequest->participant?->name}: {$validated['status']}.",
            entityType: 'PermissionRequest',
            entityId: $permissionRequest->id
        );

        $statusText = $validated['status'] === 'disetujui' ? 'disetujui' : 'ditolak';

        return back()->with('status', "Pengajuan {$permissionRequest->type} {$permissionRequest->participant?->name} berhasil {$statusText}.");
    }
}
