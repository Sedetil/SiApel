<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AuditLog;
use App\Models\Participant;
use App\Models\RfidCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(Request $request): View
    {
        $query = Participant::with('activeRfidCard');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('identifier', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('rfidCards', function ($cardQuery) use ($search) {
                        $cardQuery->where('card_uid', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('study_program')) {
            $query->where('study_program', $request->input('study_program'));
        }

        if ($request->filled('status')) {
            $statusVal = $request->input('status') === 'active' ? 1 : 0;
            $query->where('is_active', $statusVal);
        }

        $participants = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        $studyPrograms = Participant::select('study_program')
            ->whereNotNull('study_program')
            ->distinct()
            ->pluck('study_program');

        return view('pages.participants.index', compact('participants', 'studyPrograms'));
    }

    /**
     * Mode Baca: Detail profil peserta dan akumulasi riwayat kehadirannya
     */
    public function show(Participant $participant): View
    {
        $participant->load([
            'activeRfidCard',
            'rfidCards' => fn ($q) => $q->orderBy('id', 'desc'),
            'attendanceRecords' => fn ($q) => $q->with(['schedule', 'recorder'])->orderBy('check_in_time', 'desc'),
        ]);

        $totalSessions = ApelSchedule::where('status', '!=', 'cancelled')->count();
        $attendedCount = $participant->attendanceRecords->count();
        $hadirCount = $participant->attendanceRecords->where('status', 'hadir')->count();
        $terlambatCount = $participant->attendanceRecords->where('status', 'terlambat')->count();
        $attendanceRate = $totalSessions > 0 ? round(($attendedCount / $totalSessions) * 100, 1) : 0;

        return view('pages.participants.show', compact(
            'participant',
            'totalSessions',
            'attendedCount',
            'hadirCount',
            'terlambatCount',
            'attendanceRate'
        ));
    }

    public function create(): View
    {
        return view('pages.participants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => 'required|string|max:64|unique:mahasiswa,npm',
            'name' => 'required|string|max:255',
            'study_program' => 'nullable|string|max:100',
            'class_group' => 'nullable|string|max:50',
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:30',
            'initial_card_uid' => 'nullable|string|max:64',
        ], [
            'identifier.required' => 'NPM mahasiswa wajib diisi.',
            'identifier.unique' => 'NPM ini sudah terdaftar dalam sistem.',
            'name.required' => 'Nama lengkap mahasiswa wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {
            $initialUid = ! empty($validated['initial_card_uid']) ? trim(strtoupper($validated['initial_card_uid'])) : null;
            unset($validated['initial_card_uid']);

            $participant = Participant::create(array_merge($validated, ['is_active' => true]));

            if ($initialUid) {
                // Pastikan UID belum aktif pada peserta lain
                $existingActiveCard = RfidCard::where('card_uid', $initialUid)
                    ->where('is_active', true)
                    ->first();

                if ($existingActiveCard) {
                    throw new \InvalidArgumentException("Kartu RFID UID {$initialUid} sudah aktif digunakan oleh peserta lain.");
                }

                RfidCard::create([
                    'participant_id' => $participant->id,
                    'card_uid' => $initialUid,
                    'is_active' => true,
                    'issued_at' => now(),
                    'notes' => 'Pendaftaran kartu perdana saat pembuatan data peserta.',
                ]);
            }

            AuditLog::log(
                action: 'CREATE_PARTICIPANT',
                description: "Menambahkan peserta baru: {$participant->name} ({$participant->identifier}).",
                entityType: 'Participant',
                entityId: $participant->id
            );
        });

        return redirect()->route('admin.participants.index')->with('status', 'Data peserta berhasil ditambahkan.');
    }

    public function edit(Participant $participant): View
    {
        $participant->load('activeRfidCard', 'rfidCards');

        return view('pages.participants.edit', compact('participant'));
    }

    public function update(Request $request, Participant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:64', Rule::unique('mahasiswa', 'npm')->ignore($participant->id, 'id_mahasiswa')],
            'name' => 'required|string|max:255',
            'study_program' => 'nullable|string|max:100',
            'class_group' => 'nullable|string|max:50',
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'required|boolean',
        ], [
            'identifier.required' => 'NPM mahasiswa wajib diisi.',
            'identifier.unique' => 'NPM ini sudah terdaftar dalam sistem.',
            'name.required' => 'Nama lengkap mahasiswa wajib diisi.',
        ]);

        $participant->update($validated);

        AuditLog::log(
            action: 'UPDATE_PARTICIPANT',
            description: "Memperbarui data peserta: {$participant->name} ({$participant->identifier}).",
            entityType: 'Participant',
            entityId: $participant->id
        );

        return redirect()->route('admin.participants.index')->with('status', 'Data peserta berhasil diperbarui.');
    }

    public function toggleStatus(Participant $participant): RedirectResponse
    {
        $newStatus = ! $participant->is_active;
        $participant->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::log(
            action: 'TOGGLE_PARTICIPANT_STATUS',
            description: "Mengubah status peserta {$participant->name} menjadi {$statusText}.",
            entityType: 'Participant',
            entityId: $participant->id
        );

        return back()->with('status', "Status peserta {$participant->name} berhasil {$statusText}.");
    }

    /**
     * FR-03: Mendaftarkan atau memetakan kartu RFID ke peserta
     */
    public function mapRfid(Request $request, Participant $participant): RedirectResponse
    {
        $request->validate([
            'card_uid' => 'required|string|max:64',
            'notes' => 'nullable|string|max:255',
        ], [
            'card_uid.required' => 'UID Kartu RFID wajib diisi atau dipindai.',
        ]);

        $uid = trim(strtoupper($request->input('card_uid')));

        // Cek apakah UID sudah aktif digunakan peserta lain
        $duplicate = RfidCard::where('card_uid', $uid)
            ->where('is_active', true)
            ->where('participant_id', '!=', $participant->id)
            ->with('participant')
            ->first();

        if ($duplicate) {
            $ownerName = $duplicate->participant?->name ?? 'Peserta lain';

            return back()->withErrors([
                'card_uid' => "UID Kartu {$uid} sudah terhubung secara aktif ke {$ownerName} ({$duplicate->participant?->identifier}). Gunakan kartu lain atau nonaktifkan terlebih dahulu.",
            ]);
        }

        DB::transaction(function () use ($participant, $uid, $request) {
            // Nonaktifkan kartu lama peserta ini jika ada
            RfidCard::where('participant_id', $participant->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'deactivated_at' => now(),
                    'notes' => 'Dinonaktifkan karena penggantian kartu RFID baru.',
                ]);

            // Buat pemetaan kartu baru
            RfidCard::create([
                'participant_id' => $participant->id,
                'card_uid' => $uid,
                'is_active' => true,
                'issued_at' => now(),
                'notes' => $request->input('notes') ?? 'Pemetaan kartu aktif via dashboard.',
            ]);

            AuditLog::log(
                action: 'MAP_RFID',
                description: "Memetakan kartu RFID UID [{$uid}] ke peserta {$participant->name}.",
                entityType: 'Participant',
                entityId: $participant->id
            );
        });

        return back()->with('status', "Kartu RFID UID {$uid} berhasil dipetakan ke {$participant->name}.");
    }

    /**
     * FR-03: Menonaktifkan kartu RFID
     */
    public function deactivateRfid(Participant $participant): RedirectResponse
    {
        $activeCard = $participant->activeRfidCard;
        if ($activeCard) {
            $activeCard->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'notes' => 'Dinonaktifkan oleh administrator.',
            ]);

            AuditLog::log(
                action: 'DEACTIVATE_RFID',
                description: "Menonaktifkan kartu RFID UID [{$activeCard->card_uid}] dari peserta {$participant->name}.",
                entityType: 'Participant',
                entityId: $participant->id
            );

            return back()->with('status', "Kartu RFID UID {$activeCard->card_uid} berhasil dinonaktifkan.");
        }

        return back()->with('status', 'Peserta tidak memiliki kartu aktif.');
    }
}
