@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                    Live Monitoring Presensi Apel
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-sm text-[11px] font-bold bg-success/10 text-success border border-success/20">
                    <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                    <span>Monitoring Aktif</span>
                </span>
            </div>
            <p class="text-[13px] text-text-secondary mt-1">
                Pemantauan langsung aliran absensi apel mahasiswa dari pemindaian reader RFID secara real-time.
            </p>
        </div>

        <!-- Session Selector & Auto Refresh Toggle -->
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface border border-border rounded-md text-[12px] text-text-secondary">
                <span id="feed-status-dot" class="w-2 h-2 rounded-full bg-success"></span>
                <span>Pembaruan: <strong id="last-sync-time" class="font-mono text-text-primary">Langsung</strong></span>
            </div>

            <form method="GET" action="{{ route('attendance.monitor') }}" class="flex items-center gap-2">
                <select 
                    id="schedule_id" 
                    name="schedule_id" 
                    onchange="this.form.submit()"
                    class="bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary shadow-sm"
                >
                    @foreach($allSchedules as $sch)
                        <option value="{{ $sch->id }}" {{ $schedule?->id == $sch->id ? 'selected' : '' }}>
                            {{ $sch->schedule_date->format('d/m/Y') }} — {{ $sch->title }} ({{ ucfirst($sch->status) }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if(!$schedule)
        <div class="bg-surface border border-dashed border-border rounded-lg p-12 text-center shadow-sm">
            <div class="w-12 h-12 rounded-full bg-warning/10 text-warning flex items-center justify-center mx-auto mb-3">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <h2 class="text-[18px] font-bold text-text-primary">Tidak Ada Sesi Apel Aktif</h2>
            <p class="text-[14px] text-text-secondary max-w-md mx-auto mt-1 mb-4">
                Silakan pilih sesi apel lain melalui menu dropdown di atas atau hubungi Administrator untuk mengaktifkan jadwal.
            </p>
        </div>
    @else

        <!-- Active Session Meta Bar -->
        <div class="bg-surface border border-border rounded-lg p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-primary/10 text-primary flex items-center justify-center font-bold">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-[16px] font-bold text-text-primary">{{ $schedule->title }}</h2>
                        <span class="px-2 py-0.5 rounded-sm text-[10px] font-bold uppercase {{ $schedule->status === 'active' ? 'bg-success/10 text-success border border-success/20' : 'bg-background text-text-secondary border border-border' }}">
                            {{ $schedule->status }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-[12px] text-text-secondary mt-0.5">
                        <span>{{ $schedule->schedule_date->format('d M Y') }}</span>
                        <span>&bull;</span>
                        <span>Lokasi: <strong>{{ $schedule->location ?? 'Lapangan POLINELA' }}</strong></span>
                        <span>&bull;</span>
                        <span>Jam Presensi: <strong>{{ substr($schedule->attendance_start, 0, 5) }} - {{ substr($schedule->attendance_end, 0, 5) }}</strong></span>
                        <span>&bull;</span>
                        <span class="text-warning font-medium">Batas Tepat Waktu: <strong>{{ substr($schedule->late_threshold_time, 0, 5) }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Mini Live Status Badge -->
            <div class="flex items-center gap-2">
                <span class="text-[12px] text-text-secondary">Status Reader Lapangan:</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    <span>Menunggu Tap Kartu RFID</span>
                </span>
            </div>
        </div>

        <!-- 4 Key Monitoring Metric Cards (IBM Plex Mono for Stats) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Total Mahasiswa</span>
                <div class="flex items-baseline justify-between">
                    <span id="metric-total-participants" class="font-mono text-[32px] font-bold text-text-primary">{{ $stats['total_participants'] }}</span>
                    <span class="text-[11px] text-text-secondary">Peserta Aktif</span>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Tepat Waktu</span>
                <div class="flex items-baseline justify-between">
                    <span id="metric-total-hadir" class="font-mono text-[32px] font-bold text-success">{{ $stats['total_hadir'] }}</span>
                    <span class="text-[11px] text-success font-medium">Hadir &le; Batas</span>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Terlambat</span>
                <div class="flex items-baseline justify-between">
                    <span id="metric-total-terlambat" class="font-mono text-[32px] font-bold text-warning">{{ $stats['total_terlambat'] }}</span>
                    <span class="text-[11px] text-warning font-medium">Melewati Batas</span>
                </div>
            </div>

            <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                <span class="text-[11px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Total Presensi Masuk</span>
                <div class="flex items-baseline justify-between">
                    <span id="metric-total-recorded" class="font-mono text-[32px] font-bold text-primary">{{ $stats['total_recorded'] }}</span>
                    <span id="metric-attendance-rate" class="text-[11px] font-bold text-primary">
                        {{ $stats['total_participants'] > 0 ? round(($stats['total_recorded'] / $stats['total_participants']) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Monitoring Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Session Overview & Rate Breakdown (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Attendance Progress Card -->
                <div class="bg-surface border border-border rounded-lg p-5 shadow-sm space-y-4">
                    <h3 class="text-[13px] font-bold text-text-primary uppercase tracking-wider">
                        Tingkat Kehadiran Sesi
                    </h3>

                    @php
                        $rate = $stats['total_participants'] > 0 ? round(($stats['total_recorded'] / $stats['total_participants']) * 100, 1) : 0;
                    @endphp

                    <div>
                        <div class="flex items-center justify-between text-[12px] mb-1.5 font-medium">
                            <span class="text-text-secondary">Capaian Kehadiran:</span>
                            <span id="progress-percentage-label" class="font-mono font-bold text-primary">{{ $rate }}%</span>
                        </div>
                        <div class="w-full h-3 bg-background rounded-full overflow-hidden border border-border">
                            <div 
                                id="progress-bar-fill" 
                                class="h-full bg-primary transition-all duration-500 rounded-full" 
                                style="width: {{ min(100, $rate) }}%"
                            ></div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-border space-y-2 text-[12px]">
                        <div class="flex justify-between py-0.5">
                            <span class="text-text-secondary">Sudah Presensi:</span>
                            <span id="detail-recorded" class="font-mono font-bold text-text-primary">{{ $stats['total_recorded'] }} Mahasiswa</span>
                        </div>
                        <div class="flex justify-between py-0.5">
                            <span class="text-text-secondary">Belum Tercatat:</span>
                            <span id="detail-remaining" class="font-mono font-bold text-text-secondary">{{ max(0, $stats['total_participants'] - $stats['total_recorded']) }} Mahasiswa</span>
                        </div>
                    </div>
                </div>

                <!-- Monitoring Operational Notice -->
                <div class="bg-surface border border-border rounded-lg p-5 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 text-primary font-bold text-[13px]">
                        <i data-lucide="info" class="w-4 h-4"></i>
                        <span>Ketentuan Monitoring</span>
                    </div>
                    <ul class="text-[12px] text-text-secondary space-y-2 list-disc list-inside">
                        <li>Halaman ini hanya untuk <strong>memantau aliran data kehadiran</strong> tanpa opsi input manual.</li>
                        <li>Presensi sah dicatat oleh <strong>reader RFID lapangan</strong> yang memverifikasi waktu server secara terpercaya.</li>
                        <li>Sistem otomatis menandai <strong>Hadir</strong> sebelum {{ substr($schedule->late_threshold_time, 0, 5) }} dan <strong>Terlambat</strong> setelahnya.</li>
                    </ul>
                </div>

            </div>

            <!-- Right: Real-time Live Attendance Stream (8 cols) -->
            <div class="lg:col-span-8 bg-surface border border-border rounded-lg shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-border bg-background/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-[14px] font-bold text-text-primary">Aliran Presensi Real-Time</h3>
                        <p class="text-[11px] text-text-secondary">Data presensi mahasiswa yang baru saja melakukan tap kartu RFID</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-sm text-[10px] font-mono font-bold bg-success/10 text-success border border-success/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-success animate-ping"></span>
                            Live Feed
                        </span>
                    </div>
                </div>

                <!-- Table Feed -->
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-[12px]">
                        <thead>
                            <tr class="border-b border-border bg-background/80 text-[11px] font-bold uppercase text-text-secondary">
                                <th class="py-2.5 px-4">Waktu Tap</th>
                                <th class="py-2.5 px-4">NPM / Identitas</th>
                                <th class="py-2.5 px-4">Nama Mahasiswa</th>
                                <th class="py-2.5 px-4">Program Studi</th>
                                <th class="py-2.5 px-4">Status</th>
                                <th class="py-2.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="feed-table-body" class="divide-y divide-border/60">
                            @forelse($recentRecords as $rec)
                                <tr class="hover:bg-background/60 transition-colors" data-record-id="{{ $rec->id }}">
                                    <td class="py-3 px-4 font-mono font-bold text-text-primary">
                                        {{ $rec->check_in_time?->format('H:i:s') }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-text-secondary">
                                        {{ $rec->participant?->identifier }}
                                    </td>
                                    <td class="py-3 px-4 font-medium text-text-primary">
                                        {{ $rec->participant?->name }}
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary text-[11px]">
                                        {{ $rec->participant?->study_program ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($rec->status === 'hadir')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-success/10 text-success border border-success/20">
                                                HADIR
                                            </span>
                                        @elseif($rec->status === 'terlambat')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-warning/10 text-warning border border-warning/20">
                                                TERLAMBAT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-error/10 text-error border border-error/20">
                                                {{ strtoupper($rec->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($rec->participant_id)
                                            <a 
                                                href="{{ route('participants.show', $rec->participant_id) }}" 
                                                class="text-primary hover:text-primary-dark font-medium text-[11px] inline-flex items-center gap-1"
                                                title="Lihat Detail Peserta"
                                            >
                                                <span>Detail</span>
                                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                            </a>
                                        @else
                                            <span class="text-text-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-feed-row">
                                    <td colspan="6" class="py-12 text-center text-text-secondary italic">
                                        Belum ada data presensi yang masuk untuk sesi apel ini. Menunggu pemindaian RFID...
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-border bg-background/30 flex items-center justify-between text-[11px] text-text-secondary">
                    <span>Otomatis memuat data terbaru setiap 4 detik</span>
                    <a href="{{ route('attendance.history', ['schedule_id' => $schedule->id]) }}" class="text-primary hover:underline font-medium">
                        Lihat Seluruh Riwayat Sesi &rarr;
                    </a>
                </div>
            </div>

        </div>
    @endif

</div>

@if($schedule)
@push('scripts')
<script>
    const scheduleId = {{ $schedule->id }};
    const liveFeedUrl = "{{ route('attendance.live-feed') }}";
    let isPolling = true;

    async function fetchLiveFeed() {
        if (!isPolling) return;

        try {
            const response = await fetch(`${liveFeedUrl}?schedule_id=${scheduleId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;

            const data = await response.json();
            if (!data.success) return;

            // Update Metrics
            document.getElementById('metric-total-participants').textContent = data.stats.total_participants;
            document.getElementById('metric-total-hadir').textContent = data.stats.total_hadir;
            document.getElementById('metric-total-terlambat').textContent = data.stats.total_terlambat;
            document.getElementById('metric-total-recorded').textContent = data.stats.total_recorded;

            const total = data.stats.total_participants;
            const recorded = data.stats.total_recorded;
            const rate = total > 0 ? ((recorded / total) * 100).toFixed(1) : 0;

            const rateBadge = document.getElementById('metric-attendance-rate');
            if (rateBadge) rateBadge.textContent = `${rate}%`;

            const progressLabel = document.getElementById('progress-percentage-label');
            if (progressLabel) progressLabel.textContent = `${rate}%`;

            const progressFill = document.getElementById('progress-bar-fill');
            if (progressFill) progressFill.style.width = `${Math.min(100, rate)}%`;

            const detailRecorded = document.getElementById('detail-recorded');
            if (detailRecorded) detailRecorded.textContent = `${recorded} Mahasiswa`;

            const detailRemaining = document.getElementById('detail-remaining');
            if (detailRemaining) detailRemaining.textContent = `${Math.max(0, total - recorded)} Mahasiswa`;

            const syncTime = document.getElementById('last-sync-time');
            if (syncTime) syncTime.textContent = data.timestamp;

            // Update Table Body
            const tbody = document.getElementById('feed-table-body');
            if (!tbody) return;

            if (data.recent_records.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-feed-row">
                        <td colspan="6" class="py-12 text-center text-text-secondary italic">
                            Belum ada data presensi yang masuk untuk sesi apel ini. Menunggu pemindaian RFID...
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = data.recent_records.map(rec => {
                    const statusBadge = rec.status === 'hadir'
                        ? `<span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-success/10 text-success border border-success/20">HADIR</span>`
                        : (rec.status === 'terlambat'
                            ? `<span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-warning/10 text-warning border border-warning/20">TERLAMBAT</span>`
                            : `<span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-error/10 text-error border border-error/20">${rec.status.toUpperCase()}</span>`);

                    const detailUrl = `/participants/${rec.participant_id}`;

                    return `
                        <tr class="hover:bg-background/60 transition-colors" data-record-id="${rec.id}">
                            <td class="py-3 px-4 font-mono font-bold text-text-primary">${rec.check_in_time}</td>
                            <td class="py-3 px-4 font-mono text-text-secondary">${escapeHtml(rec.identifier)}</td>
                            <td class="py-3 px-4 font-medium text-text-primary">${escapeHtml(rec.name)}</td>
                            <td class="py-3 px-4 text-text-secondary text-[11px]">${escapeHtml(rec.study_program || '-')}</td>
                            <td class="py-3 px-4">${statusBadge}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="${detailUrl}" class="text-primary hover:text-primary-dark font-medium text-[11px] inline-flex items-center gap-1">
                                    <span>Detail</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }
            }

        } catch (err) {
            console.warn('Live monitoring poll error:', err);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    // Polling interval: 4 seconds
    setInterval(fetchLiveFeed, 4000);
</script>
@endpush
@endif
@endsection
