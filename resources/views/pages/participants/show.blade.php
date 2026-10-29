@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-[12px] text-text-secondary mb-1">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('participants.index') }}" class="hover:text-primary transition-colors">Data Peserta</a>
                @else
                    <a href="{{ route('timdis.participants') }}" class="hover:text-primary transition-colors">Mahasiswa Saya</a>
                @endif
                <span>&bull;</span>
                <span class="text-text-primary font-medium">Detail Peserta (Mode Baca)</span>
            </div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                {{ $participant->name }}
            </h1>
            <p class="text-[13px] text-text-secondary">
                NPM: <strong class="font-mono text-text-primary">{{ $participant->npm ?? $participant->identifier }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.participants.edit', $participant) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary hover:bg-primary-dark text-white text-[13px] font-medium rounded-md transition-colors shadow-sm">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Kelola Peserta</span>
                </a>
            @endif

            <a href="javascript:history.back()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-surface hover:bg-background border border-border text-text-primary text-[13px] font-medium rounded-md transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Content: Profile Card & Statistics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Profile Info Card (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-surface border border-border rounded-lg p-6 shadow-sm space-y-5">
                <div class="flex items-start justify-between border-b border-border pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-primary/10 text-primary font-bold text-[18px] flex items-center justify-center">
                            {{ strtoupper(substr($participant->name, 0, 2)) }}
                        </div>
                        <div>
                            <h2 class="text-[16px] font-bold text-text-primary leading-tight">{{ $participant->name }}</h2>
                            <span class="text-[12px] font-mono text-text-secondary">{{ $participant->identifier }}</span>
                        </div>
                    </div>

                    @if($participant->is_active)
                        <span class="px-2.5 py-1 rounded-sm text-[11px] font-bold bg-success/10 text-success border border-success/20">
                            Aktif
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-sm text-[11px] font-bold bg-error/10 text-error border border-error/20">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <!-- Profile Data Fields -->
                <div class="space-y-3 text-[13px]">
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <span class="text-text-secondary">Program Studi:</span>
                        <span class="font-medium text-text-primary text-right">{{ $participant->study_program ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <span class="text-text-secondary">Kelas:</span>
                        <span class="font-medium text-text-primary text-right">{{ $participant->class_group ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <span class="text-text-secondary">Jenis Kelamin:</span>
                        <span class="font-medium text-text-primary">{{ $participant->gender == 'L' ? 'Laki-laki' : ($participant->gender == 'P' ? 'Perempuan' : '-') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <span class="text-text-secondary">No. Telepon:</span>
                        <span class="font-medium text-text-primary">{{ $participant->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-border/50">
                        <span class="text-text-secondary">Kartu RFID Terhubung:</span>
                        @if($participant->activeRfidCard)
                            <span class="font-mono font-bold text-primary bg-primary/5 px-2 py-0.5 rounded-sm border border-primary/20">
                                {{ $participant->activeRfidCard->card_uid }}
                            </span>
                        @else
                            <span class="text-text-secondary italic">Belum Ada Kartu</span>
                        @endif
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-text-secondary">Terdaftar Sejak:</span>
                        <span class="text-text-primary">{{ $participant->created_at ? $participant->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                <!-- Digital QR Identity (IA requirement: Identitas, QR, RFID, riwayat) -->
                <div class="mt-4 pt-4 border-t border-border flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Digital QR Code Presensi</span>
                        <div class="font-mono font-bold text-[13px] text-slate-800">{{ $participant->identifier }}</div>
                        <p class="text-[10.5px] text-slate-500">Scan QR cadangan jika RFID tertinggal</p>
                    </div>
                    <div class="w-16 h-16 bg-white p-1 rounded-lg border border-slate-200 shadow-2xs shrink-0 flex items-center justify-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($participant->identifier) }}" 
                             alt="QR Code {{ $participant->identifier }}" 
                             class="w-full h-full object-contain">
                    </div>
                </div>
            </div>

            <!-- Mini Summary Box -->
            <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                <h3 class="text-[13px] font-bold text-text-primary uppercase tracking-wider mb-3">
                    Tingkat Kehadiran Apel
                </h3>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="font-mono text-[18px] font-bold text-primary">{{ $attendanceRate }}%</span>
                    </div>
                    <div class="text-[12px] text-text-secondary space-y-1">
                        <p>Peserta telah hadir dalam <strong>{{ $attendedCount }}</strong> dari total <strong>{{ $totalSessions }}</strong> sesi apel yang terlaksana.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Statistical Metrics & Attendance History (7 cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- 3 Stat Metrics -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-surface border border-border rounded-lg p-4 text-center shadow-sm">
                    <span class="text-[11px] font-bold text-text-secondary uppercase block mb-1">Total Hadir</span>
                    <span class="font-mono text-[26px] font-bold text-success">{{ $hadirCount }}</span>
                </div>
                <div class="bg-surface border border-border rounded-lg p-4 text-center shadow-sm">
                    <span class="text-[11px] font-bold text-text-secondary uppercase block mb-1">Terlambat</span>
                    <span class="font-mono text-[26px] font-bold text-warning">{{ $terlambatCount }}</span>
                </div>
                <div class="bg-surface border border-border rounded-lg p-4 text-center shadow-sm">
                    <span class="text-[11px] font-bold text-text-secondary uppercase block mb-1">Total Tercatat</span>
                    <span class="font-mono text-[26px] font-bold text-text-primary">{{ $attendedCount }}</span>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-background/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-[14px] font-bold text-text-primary">Riwayat Kehadiran Apel</h3>
                        <p class="text-[11px] text-text-secondary">Daftar seluruh sesi apel yang telah dihadiri peserta ini</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-sm text-[11px] font-mono bg-surface border border-border text-text-secondary">
                        {{ $participant->attendanceRecords->count() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[12px]">
                        <thead>
                            <tr class="border-b border-border bg-background/80 text-[11px] font-bold uppercase text-text-secondary">
                                <th class="py-2.5 px-4">Sesi Apel</th>
                                <th class="py-2.5 px-4">Tanggal</th>
                                <th class="py-2.5 px-4">Masuk</th>
                                <th class="py-2.5 px-4">Keluar</th>
                                <th class="py-2.5 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            @forelse($participant->attendanceRecords as $record)
                                <tr class="hover:bg-background/50 transition-colors">
                                    <td class="py-3 px-4 font-medium text-text-primary">
                                        {{ $record->schedule?->title ?? 'Sesi #' . $record->apel_schedule_id }}
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary font-mono">
                                        {{ $record->schedule?->schedule_date ? $record->schedule->schedule_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-text-primary">
                                        {{ $record->check_in_time ? $record->check_in_time->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-text-secondary">
                                        {{ $record->check_out_time ? $record->check_out_time->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($record->status === 'hadir')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-success/10 text-success border border-success/20">
                                                HADIR
                                            </span>
                                        @elseif($record->status === 'terlambat')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-warning/10 text-warning border border-warning/20">
                                                TERLAMBAT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-error/10 text-error border border-error/20">
                                                {{ strtoupper($record->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-text-secondary italic">
                                        Belum ada riwayat kehadiran apel untuk peserta ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
