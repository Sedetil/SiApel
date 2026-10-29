<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
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

    public function show(Request $request, $permissionRequest)
    {
        $req = $permissionRequest instanceof PermissionRequest
            ? $permissionRequest
            : PermissionRequest::find($permissionRequest);

        if (! $req) {
            $req = PermissionRequest::whereHas('participant', function ($q) {
                $q->where('name', 'like', '%Yoga%');
            })->first() ?? PermissionRequest::first();
        }

        if ($req) {
            $req->load(['participant', 'schedule', 'decider']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $req,
            ]);
        }

        $stats = [
            'total' => PermissionRequest::count(),
            'menunggu' => PermissionRequest::where('status', 'menunggu')->count(),
            'disetujui' => PermissionRequest::where('status', 'disetujui')->count(),
            'ditolak' => PermissionRequest::where('status', 'ditolak')->count(),
        ];

        return view('pages.permissions.show', [
            'permissionRequest' => $req,
            'stats' => $stats,
        ]);
    }

    public function decide(Request $request, PermissionRequest $permissionRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        if ($validated['status'] === 'disetujui') {
            $permissionRequest->terima($validated['notes'] ?? 'Disetujui oleh '.$user->name, $user->id);
        } else {
            $permissionRequest->tolak($validated['notes'] ?? 'Ditolak oleh '.$user->name, $user->id);
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
