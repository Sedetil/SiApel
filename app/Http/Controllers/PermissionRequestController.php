<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AuditLog;
use App\Models\Participant;
use App\Models\PermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $status = $request->query('status', 'menunggu');
        $type = $request->query('type');
        $search = $request->query('search');
        $studyProgram = $request->query('study_program');
        $department = $request->query('department');

        $query = PermissionRequest::with(['participant', 'schedule', 'decider'])
            ->latest();

        // Scope Timdis (hanya mahasiswa binaannya)
        if ($user && ! $user->isAdmin()) {
            $query->whereHas('participant', function ($q) use ($user) {
                $q->forUser($user);
            });
        }

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

        if ($studyProgram) {
            $query->whereHas('participant', function ($q) use ($studyProgram) {
                $q->where('study_program', 'like', "%{$studyProgram}%");
            });
        }

        if ($department) {
            $query->whereHas('participant', function ($q) use ($department) {
                $q->where('department', 'like', "%{$department}%");
            });
        }

        if ($request->filled('class_group')) {
            $classGroup = $request->input('class_group');
            $query->whereHas('participant', function ($q) use ($classGroup) {
                $q->where('class_group', $classGroup);
            });
        }

        $requests = $query->paginate(12)->withQueryString();

        // Query dasar untuk stats
        $baseStatsQuery = PermissionRequest::query();
        if ($user && ! $user->isAdmin()) {
            $baseStatsQuery->whereHas('participant', function ($q) use ($user) {
                $q->forUser($user);
            });
        }

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'menunggu' => (clone $baseStatsQuery)->where('status', 'menunggu')->count(),
            'disetujui' => (clone $baseStatsQuery)->where('status', 'disetujui')->count(),
            'ditolak' => (clone $baseStatsQuery)->where('status', 'ditolak')->count(),
        ];

        $schedules = ApelSchedule::latest('schedule_date')->take(10)->get();

        // Data filter dinamis sesuai role
        $departmentsQuery = Participant::select('department')->whereNotNull('department');
        if ($user && ! $user->isAdmin() && ! empty($user->department)) {
            $departmentsQuery->where('department', $user->department);
        }
        $departments = $departmentsQuery->distinct()->pluck('department');

        $studyProgramsQuery = Participant::select('study_program')->whereNotNull('study_program');
        if ($user && ! $user->isAdmin() && ! empty($user->department)) {
            $studyProgramsQuery->where('department', $user->department);
        }
        if ($department) {
            $studyProgramsQuery->where('department', $department);
        }
        $studyPrograms = $studyProgramsQuery->distinct()->pluck('study_program');

        $cohortYearsQuery = Participant::select('cohort_year')->whereNotNull('cohort_year');
        if ($user && ! $user->isAdmin() && ! empty($user->department)) {
            $cohortYearsQuery->where('department', $user->department);
        }
        $cohortYears = $cohortYearsQuery->distinct()->orderBy('cohort_year', 'desc')->pluck('cohort_year');

        return view('pages.permissions.index', compact(
            'requests',
            'stats',
            'schedules',
            'status',
            'type',
            'search',
            'studyProgram',
            'department',
            'departments',
            'studyPrograms',
            'cohortYears'
        ));
    }

    public function show(Request $request, $permissionRequest)
    {
        $user = auth()->user();

        $req = $permissionRequest instanceof PermissionRequest
            ? $permissionRequest
            : PermissionRequest::find($permissionRequest);

        // Keamanan Otorisasi: Timdis hanya bisa melihat pengajuan mahasiswa binaannya
        if ($user && ! $user->isAdmin() && $req) {
            $isBinaan = Participant::where('id', $req->participant_id)
                ->forUser($user)
                ->exists();

            if (! $isBinaan) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki wewenang untuk melihat pengajuan mahasiswa di luar lingkup binaan Anda.',
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki wewenang untuk melihat pengajuan mahasiswa di luar lingkup binaan Anda.');
            }
        }

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
        $user = auth()->user();

        // Keamanan Otorisasi: Timdis hanya bisa menyetujui/menolak pengajuan mahasiswa binaannya
        if ($user && ! $user->isAdmin()) {
            $isBinaan = Participant::where('id', $permissionRequest->participant_id)
                ->forUser($user)
                ->exists();

            if (! $isBinaan) {
                abort(403, 'Anda tidak memiliki hak akses untuk menyetujui atau menolak permohonan mahasiswa di luar binaan Anda.');
            }
        }

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
