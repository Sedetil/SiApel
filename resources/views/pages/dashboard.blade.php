@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- PAGE HEADER TITLE & GREETING -->
    <div class="page-header">
        <h1 class="text-[20px] font-bold text-slate-900 tracking-tight mb-1">
            Ringkasan absensi
        </h1>
        <p class="text-[13.5px] text-slate-500">
            Selamat sore, {{ auth()->user()->name ?? 'I Komang Artayasa' }}. Berikut aktivitas kehadiran tim Anda hari ini.
        </p>
    </div>

    <!-- FILTER BAR ROW -->
    <div class="flex flex-wrap items-center justify-between gap-3 pt-1 pb-1">
        
        <!-- Filter Controls -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Date Filter Pill -->
            <div class="bg-white border border-slate-200/90 rounded-lg px-3.5 py-1.5 text-xs text-slate-700 flex items-center gap-2.5 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="font-medium">{{ $selectedSchedule ? $selectedSchedule->schedule_date->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <!-- Jurusan Filter Pill -->
            <div class="bg-white border border-slate-200/90 rounded-lg px-3.5 py-1.5 text-xs text-slate-700 flex items-center gap-3 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors min-w-[130px] justify-between">
                <span>Semua jurusan</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
            </div>

            <!-- Lokasi Filter Pill -->
            <div class="bg-white border border-slate-200/90 rounded-lg px-3.5 py-1.5 text-xs text-slate-700 flex items-center gap-3 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors min-w-[125px] justify-between">
                <span>Semua lokasi</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
            </div>
        </div>

        <!-- Update Status & Reload Button -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
            <span>Diperbarui pukul {{ now()->format('H.i') }} WIB</span>
            <button type="button" onclick="window.location.reload()" title="Segarkan Data" class="ml-1 text-slate-400 hover:text-slate-600 transition-colors p-1">
                <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
            </button>
        </div>

    </div>

    <!-- HERO GREETING BANNER -->
    @include('pages.dashboard.hero-banner')

    <!-- STATUS MAINTENANCE ALERT BAR -->
    @include('pages.dashboard.maintenance-alert')

    <!-- THREE STATS CARDS ROW -->
    @include('pages.dashboard.stats-cards')

    <!-- CHARTS ROW (Tren Kehadiran & Distribusi Kehadiran) -->
    <div class="grid grid-cols-1 lg:grid-cols-[2.35fr_1fr] gap-4">
        
        <!-- CARD 1: Tren Kehadiran -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
            <div>
                <!-- Header with Tabs (7, 30, 90 Hari) -->
                <div class="flex items-start justify-between mb-3.5">
                    <div>
                        <h3 class="text-[13.5px] font-bold text-[#1e3a8a] leading-tight">Tren kehadiran</h3>
                        <p class="text-[11.5px] text-slate-500 mt-0.5">Kehadiran tepat waktu dan terlambat</p>
                    </div>

                    <!-- Filter Pill Tabs (Functional Links) -->
                    <div class="flex bg-slate-100 rounded-md p-0.5 gap-0.5 text-[11px]">
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['trend_days' => 7])) }}" 
                           class="px-2.5 py-1 rounded transition-all {{ $trendDays == 7 ? 'bg-white text-[#1d63dc] font-semibold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800' }}">
                            7 hari
                        </a>
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['trend_days' => 30])) }}" 
                           class="px-2.5 py-1 rounded transition-all {{ $trendDays == 30 ? 'bg-white text-[#1d63dc] font-semibold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800' }}">
                            30 hari
                        </a>
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['trend_days' => 90])) }}" 
                           class="px-2.5 py-1 rounded transition-all {{ $trendDays == 90 ? 'bg-white text-[#1d63dc] font-semibold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800' }}">
                            90 hari
                        </a>
                    </div>
                </div>

                <!-- Rate Metric Real Data -->
                <div class="flex items-baseline gap-2.5 mb-3.5">
                    <span class="text-[22px] font-bold text-[#2563eb] font-sans">{{ number_format($trendData['avg_rate'], 1, ',', '.') }}%</span>
                    <span class="text-[11px] font-bold {{ $trendData['is_positive'] ? 'text-[#16a34a]' : 'text-[#dc2626]' }} flex items-center gap-1">
                        <i data-lucide="{{ $trendData['is_positive'] ? 'trending-up' : 'trending-down' }}" class="w-3.5 h-3.5"></i> 
                        {{ $trendData['is_positive'] ? '+' : '' }}{{ number_format($trendData['diff_poin'], 1, ',', '.') }} poin
                    </span>
                    <span class="text-[11px] text-slate-400">vs {{ $trendDays }} hari sebelumnya</span>
                </div>
            </div>

            <!-- Dynamic Area Line Chart SVG Container -->
            <div class="relative w-full h-[155px] mt-1 select-none">
                <svg width="100%" height="100%" viewBox="0 0 650 150" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.32"/>
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <!-- Horizontal Grid Lines -->
                    <line x1="35" y1="25" x2="640" y2="25" stroke="#f1f5f9" stroke-width="1"/>
                    <line x1="35" y1="75" x2="640" y2="75" stroke="#f1f5f9" stroke-width="1"/>
                    <line x1="35" y1="125" x2="640" y2="125" stroke="#f1f5f9" stroke-width="1"/>

                    <!-- Y-Axis Dynamic Labels -->
                    <text x="0" y="29" fill="#94a3b8" font-size="10" font-family="Inter, sans-serif">{{ $trendData['y_labels']['top'] }}</text>
                    <text x="0" y="79" fill="#94a3b8" font-size="10" font-family="Inter, sans-serif">{{ $trendData['y_labels']['mid'] }}</text>
                    <text x="0" y="129" fill="#94a3b8" font-size="10" font-family="Inter, sans-serif">{{ $trendData['y_labels']['bottom'] }}</text>

                    @if(!empty($trendData['area_path']))
                        <!-- Shaded Gradient Area -->
                        <path d="{{ $trendData['area_path'] }}" fill="url(#chartGrad)"/>
                        <!-- Primary Blue Stroke Line -->
                        <path d="{{ $trendData['line_path'] }}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif

                    <!-- Tooltip Pill on Latest Data Point -->
                    @if(isset($trendData['latest_point']))
                        <rect x="{{ $trendData['latest_point']['pill_x'] }}" y="{{ $trendData['latest_point']['pill_y'] }}" width="42" height="18" rx="4" fill="#2563eb"/>
                        <text x="{{ $trendData['latest_point']['text_x'] }}" y="{{ $trendData['latest_point']['text_y'] }}" fill="#ffffff" font-size="9" font-weight="700" text-anchor="middle" font-family="Inter, sans-serif">{{ number_format($trendData['latest_point']['rate'], 1, ',', '.') }}%</text>
                        <circle cx="{{ $trendData['latest_point']['x'] }}" cy="{{ $trendData['latest_point']['y'] }}" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
                    @endif

                    <!-- Interactive Hover Nodes -->
                    @foreach($trendData['points'] as $pt)
                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#2563eb" class="opacity-0 hover:opacity-100 cursor-pointer transition-opacity">
                            <title>{{ $pt['date_full'] }}: {{ number_format($pt['rate'], 1, ',', '.') }}% ({{ $pt['attended'] }} hadir / {{ $pt['total'] }} mhs)</title>
                        </circle>
                    @endforeach

                    <!-- X-Axis Date Labels -->
                    @foreach($trendData['axis_labels'] as $lbl)
                        <text x="{{ $lbl['x'] }}" y="145" fill="{{ $lbl['is_last'] ? '#2563eb' : '#94a3b8' }}" {{ $lbl['is_last'] ? 'font-weight="700"' : '' }} font-size="10" font-family="Inter, sans-serif" text-anchor="{{ $lbl['is_last'] ? 'end' : ($loop->first ? 'start' : 'middle') }}">
                            {{ $lbl['text'] }}
                        </text>
                    @endforeach
                </svg>
            </div>
        </div>

        <!-- CARD 2: Distribusi Kehadiran (Real Database Data) -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
            <div>
                <h3 class="text-[13.5px] font-bold text-[#1e3a8a] leading-tight">Distribusi kehadiran</h3>
                <p class="text-[11.5px] text-slate-500 mt-0.5">{{ $selectedSchedule ? $selectedSchedule->schedule_date->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y') }}</p>

                <div class="flex gap-4 items-center mt-3 mb-4">
                    <!-- Highlight Box -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 text-center flex-[1.1]">
                        <div class="text-[10.5px] text-slate-500 mb-1">Tingkat kehadiran</div>
                        <div class="text-[22px] font-bold text-[#2563eb] font-sans leading-none">{{ number_format($stats['attendance_rate'], 1, ',', '.') }}%</div>
                        <div class="text-[10px] text-blue-500 font-medium my-1.5">{{ $stats['session_total'] }} dari {{ $stats['total_participants'] }} orang</div>
                        
                        <!-- Multi-Color Progress Bar (Proportional Real Data) -->
                        <div class="h-1.5 w-full bg-slate-200 rounded-full flex overflow-hidden my-2">
                            <div style="width: {{ $stats['distribution']['hadir_pct'] }}%;" class="bg-[#2563eb]" title="Hadir tepat waktu: {{ $stats['session_hadir'] }} ({{ number_format($stats['distribution']['hadir_pct'], 1, ',', '.') }}%)"></div>
                            <div style="width: {{ $stats['distribution']['terlambat_pct'] }}%;" class="bg-[#f59e0b]" title="Terlambat: {{ $stats['session_terlambat'] }} ({{ number_format($stats['distribution']['terlambat_pct'], 1, ',', '.') }}%)"></div>
                            <div style="width: {{ $stats['distribution']['izin_sakit_pct'] }}%;" class="bg-[#6366f1]" title="Izin & Sakit: {{ $stats['session_izin'] + $stats['session_sakit'] }} ({{ number_format($stats['distribution']['izin_sakit_pct'], 1, ',', '.') }}%)"></div>
                            <div style="width: {{ $stats['distribution']['tidak_hadir_pct'] }}%;" class="bg-[#f87171]" title="Tidak hadir: {{ $stats['session_tidak_hadir'] }} ({{ number_format($stats['distribution']['tidak_hadir_pct'], 1, ',', '.') }}%)"></div>
                        </div>

                        <div class="text-[9.5px] text-slate-400">Komposisi sesi ini</div>
                    </div>

                    <!-- Legend Items Real Data -->
                    <div class="flex-1 flex flex-col gap-2 text-[11px]">
                        <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb] mt-1 shrink-0"></span>
                            <div>
                                <div class="text-[#1e3a8a] font-semibold leading-tight">Hadir tepat waktu</div>
                                <div class="text-slate-500 text-[10.5px]">{{ number_format($stats['distribution']['hadir_pct'], 1, ',', '.') }}% ({{ $stats['session_hadir'] }} mhs)</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#f59e0b] mt-1 shrink-0"></span>
                            <div>
                                <div class="text-[#1e3a8a] font-semibold leading-tight">Terlambat</div>
                                <div class="text-slate-500 text-[10.5px]">{{ number_format($stats['distribution']['terlambat_pct'], 1, ',', '.') }}% ({{ $stats['session_terlambat'] }} mhs)</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#6366f1] mt-1 shrink-0"></span>
                            <div>
                                <div class="text-[#1e3a8a] font-semibold leading-tight">Izin & Sakit</div>
                                <div class="text-slate-500 text-[10.5px]">{{ number_format($stats['distribution']['izin_sakit_pct'], 1, ',', '.') }}% ({{ $stats['session_izin'] + $stats['session_sakit'] }} mhs)</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#f87171] mt-1 shrink-0"></span>
                            <div>
                                <div class="text-[#1e3a8a] font-semibold leading-tight">Tidak hadir</div>
                                <div class="text-slate-500 text-[10.5px]">{{ number_format($stats['distribution']['tidak_hadir_pct'], 1, ',', '.') }}% ({{ $stats['session_tidak_hadir'] }} mhs)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Badge Real Data -->
            <div class="bg-[#ecfdf5] text-[#15803d] rounded-lg px-3 py-2 text-[11px] font-medium flex items-center gap-2 border border-emerald-100">
                <i data-lucide="trending-up" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>{{ $stats['session_total'] }} dari {{ $stats['total_participants'] }} Mahasiswa tercatat hadir{{ $selectedSchedule ? ' pada sesi ini.' : ' hari ini.' }}</span>
            </div>
        </div>

    </div>

    <!-- BOTTOM ROW: JADWAL APEL & IZIN MENUNGGU -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.25fr] gap-4">
        
        <!-- CARD: Jadwal Apel -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3.5">
                    <h3 class="text-[13.5px] font-bold text-slate-800 leading-tight">Jadwal apel</h3>
                    <a href="{{ route('schedules.index') }}" class="text-[11.5px] text-[#1d63dc] font-semibold hover:underline flex items-center gap-1">
                        <span>Kelola jadwal</span> &rarr;
                    </a>
                </div>

                @forelse($upcomingSchedules as $sched)
                    <div class="flex items-start justify-between py-2.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                        <div class="flex gap-3">
                            <div class="w-[42px] h-[44px] bg-[#eff6ff] rounded-lg flex flex-col items-center justify-center text-[#1d63dc] shrink-0 border border-blue-100">
                                <span class="text-[9px] font-extrabold uppercase">{{ $sched->schedule_date->translatedFormat('M') }}</span>
                                <strong class="text-[15px] font-extrabold leading-none">{{ $sched->schedule_date->format('d') }}</strong>
                            </div>
                            <div>
                                <div class="font-bold text-[12.5px] text-slate-800 leading-tight mb-0.5">{{ $sched->title }}</div>
                                <div class="text-[11px] text-slate-500 mb-0.5">{{ $sched->schedule_date->translatedFormat('l') }} &middot; {{ substr($sched->start_time, 0, 5) }}–{{ substr($sched->end_time, 0, 5) }} WIB &middot; {{ $sched->location }}</div>
                                <div class="text-[10.5px] text-slate-400">Kategori: {{ ucfirst($sched->category ?? 'rutin') }} &middot; {{ $sched->attendance_records_count ?? 0 }} hadir</div>
                            </div>
                        </div>
                        @php
                            $schedBadge = match($sched->status) {
                                'active' => 'bg-[#dcfce7] text-[#16a34a]',
                                'scheduled' => 'bg-[#e0f2fe] text-[#0369a1]',
                                'completed' => 'bg-slate-100 text-slate-600',
                                'cancelled' => 'bg-rose-50 text-rose-600',
                                default => 'bg-slate-100 text-slate-600'
                            };
                        @endphp
                        <span class="{{ $schedBadge }} font-semibold text-[10.5px] px-2.5 py-1 rounded-md shrink-0">
                            {{ ucfirst($sched->status) }}
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-[12px]">Belum ada jadwal apel</div>
                @endforelse
            </div>
        </div>

        <!-- CARD: Izin & Sakit Menunggu (IA: Verifikasi & Approval) -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-[13.5px] font-bold text-slate-800 leading-tight">Izin & sakit menunggu</h3>
                <a href="{{ route('permission-requests.index') }}" class="text-[11.5px] text-[#1d63dc] font-semibold hover:underline flex items-center gap-1">
                    <span>Lihat {{ $stats['pending_permissions'] }} pengajuan</span> &rarr;
                </a>
            </div>

            <!-- Warning Banner -->
            <div class="bg-[#fffbeb] border border-amber-200/60 rounded-lg px-3 py-2 text-[#b45309] text-[11px] flex items-center gap-2 mb-2.5">
                <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i>
                <span>{{ $stats['pending_izin'] }} izin &middot; {{ $stats['pending_sakit'] }} sakit — validasi pengajuan dilakukan oleh Timdis.</span>
            </div>

            @forelse($recentPermissions as $perm)
                <a href="{{ route('permission-requests.index') }}" class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }} hover:bg-slate-50/50 px-1 rounded transition-colors group">
                    <div>
                        <div class="font-bold text-[12px] text-slate-800 group-hover:text-primary transition-colors">{{ $perm->participant->name ?? 'Mahasiswa' }}</div>
                        <div class="text-[10.5px] text-slate-400">{{ $perm->participant->nim ?? '-' }} &middot; {{ ucfirst($perm->type) }} &middot; {{ $perm->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <div class="text-[10px] text-slate-400">Status</div>
                            <span class="inline-block text-[10.5px] font-semibold {{ $perm->status === 'menunggu' ? 'text-amber-600' : ($perm->status === 'disetujui' ? 'text-green-600' : 'text-rose-600') }}">
                                {{ ucfirst($perm->status) }}
                            </span>
                        </div>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-300 group-hover:text-primary transition-colors"></i>
                    </div>
                </a>
            @empty
                <div class="py-6 text-center text-slate-400 text-[12px]">Tidak ada permohonan izin/sakit</div>
            @endforelse
        </div>

    </div>

    <!-- FOOTER CARDS: E-KULIAH & AKUN ADMIN -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.25fr] gap-4 pt-1">
        
        <!-- Footer Card 1: SiAbsen E-Kuliah -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#eff6ff] text-[#2563eb] flex items-center justify-center text-sm shrink-0 border border-blue-100">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <strong class="text-[12.5px] text-slate-800 font-bold">SiAbsen E-Kuliah</strong>
                        <span class="bg-[#dcfce7] text-[#16a34a] font-semibold text-[9.5px] px-2 py-0.5 rounded-md">Terhubung</span>
                    </div>
                    <div class="text-[10.5px] text-slate-400">{{ $stats['total_participants'] }} data mahasiswa tersinkron &middot; API Service Aktif</div>
                </div>
            </div>
            <a href="{{ route('ekuliah.index') }}" class="bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-[11.5px] font-semibold px-3 py-1.5 rounded-md transition-colors shrink-0 shadow-sm text-center">
                Kelola integrasi
            </a>
        </div>

        <!-- Footer Card 2: Akun Admin -->
        <div class="bg-white border border-slate-200/90 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#eff6ff] text-[#2563eb] flex items-center justify-center text-sm shrink-0 border border-blue-100">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="font-bold text-[12.5px] text-slate-800 mb-0.5">Akun Petugas & Admin</div>
                    <div class="text-[10.5px] text-slate-400">{{ $stats['total_admins'] }} Admin &middot; {{ $stats['total_timdis'] }} Timdis aktif</div>
                </div>
            </div>
            @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('officers.index') }}" class="text-[11.5px] text-[#1d63dc] font-semibold hover:underline flex items-center gap-1 shrink-0">
                    <span>Kelola akun</span> &rarr;
                </a>
            @else
                <span class="text-[11.5px] text-slate-400">Hanya Admin</span>
            @endif
        </div>

    </div>

</div>
@endsection
