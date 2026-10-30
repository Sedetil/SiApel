@extends('layouts.app')

@php
    $breadcrumbCategory = 'Menu Utama';
    $breadcrumbPage = 'Riwayat Abensi';

    // Model data extraction
    $participant = $permissionRequest->participant ?? null;
    $schedule = $permissionRequest->schedule ?? null;
    $decider = $permissionRequest->decider ?? null;

    $name = $participant?->name ?? 'Yoga Saputra';
    $nameParts = explode(' ', trim($name));
    $initials = strtoupper(substr($nameParts[0] ?? 'Y', 0, 1) . substr($nameParts[1] ?? 'S', 0, 1));
    if (strlen($initials) < 2) $initials = strtoupper(substr($name, 0, 2));

    $prodi = $participant?->study_program ?? 'Teknologi Rekayasa Perangkat Lunak';
    $status = $permissionRequest->status ?? 'menunggu';
    $type = $permissionRequest->type ?? 'sakit';
    $typeLabel = $type === 'sakit' ? 'Izin Sakit' : 'Izin Keperluan';

    // Dates
    $createdAt = $permissionRequest->created_at ? \Carbon\Carbon::parse($permissionRequest->created_at) : \Carbon\Carbon::parse('2026-10-20 16:30:00');
    $submittedDate = $createdAt->translatedFormat('d M Y · H.i') . ' WIB';

    $scheduleDate = $schedule?->schedule_date ? \Carbon\Carbon::parse($schedule->schedule_date) : \Carbon\Carbon::parse('2026-10-21');
    $dateFormatted = $scheduleDate->translatedFormat('l, d F Y');
    $scheduleTitle = $schedule?->title ?? 'Apel di hari senin';

    // Relative badge
    $relativeBadge = 'Besok';
    if ($scheduleDate->isToday()) {
        $relativeBadge = 'Hari ini';
    } elseif ($scheduleDate->isPast()) {
        $relativeBadge = 'Selesai';
    }

    $reason = $permissionRequest->reason ?: 'Selamat pagi, mohon izin menyampaikan bahwa hari ini saya tidak dapat mengikuti apel dikarenakan kondisi badan sedang kurang sehat/sakit. Mohon izin untuk beristirahat. Terima kasih.';
    $attachment = $permissionRequest->attachment_path;

    $waitingCount = ($stats['menunggu'] ?? 0) > 0 ? $stats['menunggu'] : 8;
@endphp

@section('content')
<div class="space-y-4 max-w-[1240px] mx-auto pb-10">

    <!-- 1. JUDUL HALAMAN & SUB-JUDUL -->
    <div class="pt-1 pb-1">
        <h1 class="text-[26px] font-bold text-slate-900 tracking-tight leading-tight">
            Pengajuan izin
        </h1>
        <p class="text-[13.5px] text-slate-500 mt-0.5">
            Tinjau dan kelola pengajuan izin Sakit Mahasiswa
        </p>
    </div>

    <!-- Feedback Flash Messages -->
    @if (session('status'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[12.5px] font-medium flex items-center gap-2.5 shadow-xs">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif


    <!-- 3. KARTU UTAMA: DETAIL PENGAJUAN -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 sm:p-7 space-y-6">

        <!-- Card Header: Title & Status Badge -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ role_route('permission-requests.index') }}" class="p-1.5 -ml-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Kembali ke Daftar">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <h2 class="text-[16px] font-bold text-slate-900 tracking-tight">Detail pengajuan</h2>
            </div>

            <!-- Status Pill Badge -->
            @if($status === 'menunggu')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/90">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>Menunggu</span>
                </span>
            @elseif($status === 'disetujui')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/90">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Disetujui</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-red-50 text-red-700 border border-red-200/90">
                    <i data-lucide="x-circle" class="w-3.5 h-3.5 text-red-600"></i>
                    <span>Ditolak</span>
                </span>
            @endif
        </div>

        <!-- Student Profile Row -->
        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-600 font-bold text-[13.5px] flex items-center justify-center shrink-0 shadow-xs">
                    {{ $initials }}
                </div>
                <div>
                    <h3 class="text-[15px] font-bold text-slate-900 leading-snug">
                        {{ $name }}
                    </h3>
                    <p class="text-[12.5px] text-slate-500 font-normal">
                        {{ $prodi }}
                    </p>
                </div>
            </div>

            <a href="{{ $participant ? role_route('participants.show', $participant->id) : '#' }}" 
               class="text-[12.5px] font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1 transition-colors">
                <span>Lihat profil</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <!-- Bordered Information Box (Jenis, Durasi, Tanggal Apel) -->
        <div class="border border-slate-200/90 rounded-xl p-4 bg-white space-y-4 shadow-2xs">
            <!-- Grid 2 Columns -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-[11.5px] text-slate-400 font-medium">Jenis pengajuan</span>
                    <span class="block text-[14px] font-bold text-slate-900 mt-0.5">
                        {{ $typeLabel }}
                    </span>
                </div>
                <div>
                    <span class="block text-[11.5px] text-slate-400 font-medium">Durasi</span>
                    <div class="mt-0.5">
                        <span class="text-[14px] font-bold text-slate-900">1 hari</span>
                        <span class="block text-[11px] text-slate-400 font-normal">1 hari apel</span>
                    </div>
                </div>
            </div>

            <!-- Calendar Row -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[13px] font-bold text-slate-900">
                            {{ $dateFormatted }}
                        </div>
                        <div class="text-[11.5px] text-slate-400">
                            {{ $scheduleTitle }}
                        </div>
                    </div>
                </div>

                @if($relativeBadge)
                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200/80">
                        {{ $relativeBadge }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Section: Alasan Pengajuan -->
        <div class="space-y-2 pt-1">
            <h4 class="text-[13px] font-bold text-slate-900">Alasan pengajuan</h4>
            <p class="text-[13px] text-slate-700 leading-relaxed font-normal">
                {{ $reason }}
            </p>

            <!-- Attachment Box -->
            <div class="border border-slate-200/90 rounded-xl p-3 bg-white flex items-center justify-between text-[12.5px] mt-2.5">
                <div class="flex items-center gap-2.5 text-slate-500">
                    <i data-lucide="paperclip" class="w-4 h-4 text-slate-400 shrink-0"></i>
                    @if($attachment)
                        <span class="font-medium text-slate-700 truncate max-w-[280px]">{{ basename($attachment) }}</span>
                    @else
                        <span class="text-slate-500 font-normal">Tidak ada lampiran yang disertakan.</span>
                    @endif
                </div>
                @if($attachment)
                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-[12px] font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                        <span>Lihat lampiran</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Section: Riwayat Pengajuan (Timeline) -->
        <div class="space-y-2.5 pt-1">
            <h4 class="text-[13px] font-bold text-slate-900">Riwayat pengajuan</h4>
            <div class="space-y-2 text-[12.5px]">
                
                <!-- Submission Timestamp -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: #2D56FF;"></span>
                        <span class="text-slate-800 font-normal">Diajukan oleh {{ $name }}</span>
                    </div>
                    <span class="text-slate-400 text-[11.5px] font-mono">{{ $submittedDate }}</span>
                </div>

                <!-- Decision / Status Timeline Item -->
                @if($status === 'menunggu')
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: #F5A623;"></span>
                        <span class="text-amber-700 font-medium">Menunggu persetujuan Admin SDM</span>
                    </div>
                @elseif($status === 'disetujui')
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: #22C55E;"></span>
                            <span class="text-emerald-700 font-medium">Disetujui oleh {{ $decider?->name ?? 'Admin Timdis' }}</span>
                        </div>
                        <span class="text-slate-400 text-[11.5px] font-mono">{{ $permissionRequest->decided_at ? \Carbon\Carbon::parse($permissionRequest->decided_at)->translatedFormat('d M Y · H.i') . ' WIB' : '-' }}</span>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: #EF4444;"></span>
                            <span class="text-red-700 font-medium">Ditolak oleh {{ $decider?->name ?? 'Admin Timdis' }}</span>
                        </div>
                        <span class="text-slate-400 text-[11.5px] font-mono">{{ $permissionRequest->decided_at ? \Carbon\Carbon::parse($permissionRequest->decided_at)->translatedFormat('d M Y · H.i') . ' WIB' : '-' }}</span>
                    </div>
                @endif

            </div>
        </div>

        <!-- Section: Catatan Keputusan & Action Form -->
        <form method="POST" action="{{ role_route('permission-requests.decide', $permissionRequest->id) }}" class="space-y-4 pt-1">
            @csrf
            <div>
                <div class="flex items-center gap-1.5 mb-1.5">
                    <label for="notes" class="text-[13px] font-bold text-slate-900">
                        Catatan keputusan
                    </label>
                    <span class="text-[12px] text-slate-400 font-normal">
                        (opsional jika disetujui)
                    </span>
                </div>
                <textarea 
                    name="notes" 
                    id="notes" 
                    rows="3" 
                    placeholder="Tambahkan catatan untuk karyawan..." 
                    class="w-full border border-slate-200/90 rounded-xl p-3 text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors bg-white resize-y"
                    {{ $status !== 'menunggu' ? 'readonly' : '' }}
                >{{ $permissionRequest->notes }}</textarea>
                <p class="text-[11.5px] text-slate-400 mt-1">
                    Alasan wajib diisi jika pengajuan ditolak.
                </p>
            </div>

            <!-- Action Buttons: Tolak & Setujui -->
            @if($status === 'menunggu')
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        name="status" 
                        value="ditolak" 
                        class="w-full sm:w-1/2 py-2.5 px-5 rounded-xl border border-red-200 bg-white hover:bg-red-50 text-red-600 font-semibold text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Tolak</span>
                    </button>

                    <button 
                        type="submit" 
                        name="status" 
                        value="disetujui" 
                        class="w-full sm:w-1/2 py-2.5 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                        style="background-color: #4D7BFF !important; color: #ffffff !important;"
                    >
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Setujui pengajuan</span>
                    </button>
                </div>
                <p class="text-[11.5px] text-slate-400 text-center block pt-0.5">
                    Karyawan akan menerima notifikasi setelah keputusan diberikan.
                </p>
            @else
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-[12.5px] flex items-center justify-between">
                    <span class="font-medium">Status Keputusan: <strong class="capitalize {{ $status === 'disetujui' ? 'text-emerald-700' : 'text-red-700' }}">{{ $status }}</strong></span>
                    <a href="{{ role_route('permission-requests.index') }}" class="text-blue-600 font-semibold hover:underline">Kembali ke Daftar Pengajuan</a>
                </div>
            @endif
        </form>

    </div>

</div>
@endsection
