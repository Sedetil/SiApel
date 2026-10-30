@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER TITLE -->
    <div class="page-header">
        <h1 class="text-[24px] font-bold text-slate-900 tracking-tight leading-tight">
            Dashboard
        </h1>
        <p class="text-[13px] text-slate-500 mt-1">
            Pantau ringkasan kehadiran, tren, dan jadwal apel mahasiswa secara real-time.
        </p>
    </div>

    <!-- MAIN TWO COLUMN GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-[1.48fr_1fr] gap-6 items-start">
        
        <!-- ============================================== -->
        <!-- LEFT COLUMN (Banner, Tren Kehadiran, Jadwal) -->
        <!-- ============================================== -->
        <div class="space-y-6">
            
            <!-- 1. Selamat Datang Hero Banner -->
            @include('pages.dashboard.hero-banner')

            <!-- 2. Tren Kehadiran Card -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
                <div>
                    <!-- Header with Dropdown / Filter -->
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-[14px] font-bold text-[#2D56FF] leading-tight">Tren kehadiran</h3>
                            <p class="text-[12px] text-slate-400 mt-0.5">Kehadiran tepat waktu dan terlambat</p>
                        </div>

                        <!-- Date/Schedule Dropdown Select -->
                        <div class="relative">
                            <form method="GET" action="{{ route('dashboard') }}" id="schedule-select-form">
                                <select 
                                    name="schedule_id" 
                                    onchange="document.getElementById('schedule-select-form').submit()"
                                    class="text-xs text-slate-600 bg-white border border-slate-200 rounded-lg px-3 py-1.5 pr-7 font-medium cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary appearance-none hover:bg-slate-50 transition-colors"
                                >
                                    <option value="">Pilih Tanggal Apel</option>
                                    @foreach($allSchedules as $as)
                                        <option value="{{ $as->id }}" {{ $selectedSchedule && $selectedSchedule->id === $as->id ? 'selected' : '' }}>
                                            {{ $as->schedule_date->translatedFormat('d M Y') }} - {{ Str::limit($as->title, 18) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2 top-2.5 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Rate Metric Data -->
                    <div class="flex items-baseline gap-2.5 mb-4">
                        <span class="text-[26px] font-bold text-[#2D56FF] font-sans">{{ number_format($trendData['avg_rate'], 1, ',', '.') }}%</span>
                        <span class="text-[11.5px] font-bold {{ $trendData['is_positive'] ? 'text-[#16a34a]' : 'text-[#dc2626]' }} flex items-center gap-1">
                            <i data-lucide="{{ $trendData['is_positive'] ? 'trending-up' : 'trending-down' }}" class="w-3.5 h-3.5"></i> 
                            {{ number_format($trendData['diff_poin'], 1, ',', '.') }} poin
                        </span>
                        <span class="text-[11.5px] text-slate-400">vs {{ $trendDays }} hari sebelumnya</span>
                    </div>
                </div>

                <!-- Area Line Chart SVG Container -->
                <div class="relative w-full h-[160px] select-none">
                    <svg width="100%" height="100%" viewBox="0 0 650 150" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.22"/>
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

                        <!-- Hover Nodes -->
                        @foreach($trendData['points'] as $pt)
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4" fill="#2563eb" class="opacity-0 hover:opacity-100 cursor-pointer transition-opacity">
                                <title>{{ $pt['date_full'] }}: {{ number_format($pt['rate'], 1, ',', '.') }}% ({{ $pt['attended'] }} hadir)</title>
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

            <!-- 3. Jadwal Apel Card -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Jadwal apel</h3>
                </div>

                <div class="space-y-4">
                    @forelse($upcomingSchedules->take(2) as $index => $sched)
                        <div class="flex items-start justify-between py-2 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                            <div class="flex items-start gap-3.5">
                                @if($index === 0)
                                    <!-- Date Box (OKT 21) -->
                                    <div class="w-12 h-12 bg-[#EEF2FF] rounded-xl flex flex-col items-center justify-center text-[#2D56FF] shrink-0 border border-blue-100">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-blue-500">{{ $sched->schedule_date->translatedFormat('M') }}</span>
                                        <strong class="text-[17px] font-extrabold leading-none">{{ $sched->schedule_date->format('d') }}</strong>
                                    </div>
                                @else
                                    <!-- Calendar Icon for next schedule -->
                                    <div class="w-10 h-10 flex items-center justify-center text-slate-400 shrink-0">
                                        <i data-lucide="calendar" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                @endif

                                <div>
                                    <div class="font-bold text-[13px] text-slate-800 leading-tight">
                                        @if($index === 1)
                                            <span class="text-slate-600 font-semibold">Berikutnya &middot;</span>
                                        @endif
                                        {{ $sched->title }}
                                    </div>
                                    <div class="text-[11.5px] text-slate-500 mt-0.5">
                                        {{ $sched->schedule_date->translatedFormat('l') }} &middot; {{ substr($sched->start_time, 0, 5) }}–{{ substr($sched->end_time, 0, 5) }} WIB &middot; {{ $sched->location }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ number_format($stats['total_participants']) }} mahasiswa &middot; Penanggung jawab: {{ $sched->creator->name ?? 'Agus Saputra' }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if($sched->status === 'completed' || $sched->status === 'active')
                                    <span class="bg-[#E6F8EF] text-[#22C55E] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        Selesai
                                    </span>
                                @else
                                    <span class="bg-[#EFF6FF] text-[#2D56FF] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        Terjadwal
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">Belum ada jadwal apel</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- RIGHT COLUMN (Total Mhs, 4 Stats, Distribusi)  -->
        <!-- ============================================== -->
        <div class="space-y-5">
            
            <!-- 1. Total Mahasiswa Saya (Blue Hero Card) -->
            @include('pages.dashboard.stats-cards')

            <!-- 2. Four Metrics Grid (2x2) -->
            @include('pages.dashboard.metric-cards')

            <!-- 3. Distribusi Kehadiran Card -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-[14px] font-bold text-[#2D56FF] leading-tight">Distribusi kehadiran</h3>
                            <p class="text-[11.5px] text-slate-400 mt-0.5">{{ $selectedSchedule ? $selectedSchedule->schedule_date->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <span class="bg-[#EEF2FF] text-[#2D56FF] font-semibold text-[11px] px-2.5 py-1 rounded-lg">
                            {{ number_format($stats['session_total']) }} orang
                        </span>
                    </div>

                    <div class="flex gap-4 items-center mt-2 mb-4">
                        <!-- Highlight Percentage Box -->
                        <div class="bg-[#F8FAFC] border border-slate-200/80 rounded-xl p-3 text-center flex-[1]">
                            <div class="text-[10px] text-slate-500 mb-1">Tingkat kehadiran</div>
                            <div class="text-[24px] font-bold text-[#2D56FF] font-sans leading-none">{{ number_format($stats['attendance_rate'], 1, ',', '.') }}%</div>
                            <div class="text-[9.5px] text-slate-400 my-1">{{ $stats['session_total'] }} dari {{ $stats['total_participants'] }} orang</div>
                            
                            <!-- Segmented Multi-Color Progress Bar -->
                            <div class="h-1.5 w-full bg-slate-200 rounded-full flex overflow-hidden my-1.5">
                                <div style="width: {{ $stats['distribution']['hadir_pct'] }}%;" class="bg-[#2D56FF]"></div>
                                <div style="width: {{ $stats['distribution']['terlambat_pct'] }}%;" class="bg-[#F5A623]"></div>
                                <div style="width: {{ $stats['distribution']['izin_sakit_pct'] }}%;" class="bg-[#60A5FA]"></div>
                                <div style="width: {{ $stats['distribution']['tidak_hadir_pct'] }}%;" class="bg-[#FF4D4F]"></div>
                            </div>

                            <div class="text-[9px] text-slate-400">Komposisi hari ini</div>
                        </div>

                        <!-- Legend Items with Numbers on the right -->
                        <div class="flex-[1.2] flex flex-col gap-2 text-[11.5px]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#2D56FF] shrink-0"></span>
                                    <div>
                                        <div class="text-slate-700 font-semibold leading-tight">Hadir tepat waktu</div>
                                        <div class="text-slate-400 text-[10px]">{{ number_format($stats['distribution']['hadir_pct'], 1, ',', '.') }}%</div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-800 font-sans text-xs">{{ number_format($stats['session_hadir']) }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#F5A623] shrink-0"></span>
                                    <div>
                                        <div class="text-slate-700 font-semibold leading-tight">Terlambat</div>
                                        <div class="text-slate-400 text-[10px]">{{ number_format($stats['distribution']['terlambat_pct'], 1, ',', '.') }}%</div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-800 font-sans text-xs">{{ number_format($stats['session_terlambat']) }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#60A5FA] shrink-0"></span>
                                    <div>
                                        <div class="text-slate-700 font-semibold leading-tight">Izin & Sakit</div>
                                        <div class="text-slate-400 text-[10px]">{{ number_format($stats['distribution']['izin_sakit_pct'], 1, ',', '.') }}%</div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-800 font-sans text-xs">{{ number_format($stats['session_izin'] + $stats['session_sakit']) }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF4D4F] shrink-0"></span>
                                    <div>
                                        <div class="text-slate-700 font-semibold leading-tight">Tidak hadir</div>
                                        <div class="text-slate-400 text-[10px]">{{ number_format($stats['distribution']['tidak_hadir_pct'], 1, ',', '.') }}%</div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-800 font-sans text-xs">{{ number_format($stats['session_tidak_hadir']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Pill Note -->
                <div class="bg-[#E6F8EF] text-[#22C55E] rounded-xl px-3.5 py-2 text-[11px] font-medium flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[#22C55E] shrink-0"></i>
                    <span>{{ $stats['session_total'] }} dari {{ $stats['total_participants'] }} mahasiswa tercatat hadir hari ini.</span>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
