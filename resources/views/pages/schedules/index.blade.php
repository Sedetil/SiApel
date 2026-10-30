@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Manajemen Jadwal Apel
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Atur jadwal pelaksanaan apel, jam pembukaan presensi, dan batas toleransi keterlambatan.
            </p>
        </div>

        <a href="{{ route('schedules.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-4 py-2.5 rounded-md text-[13px] font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Jadwal Baru</span>
        </a>
    </div>

    <!-- Filter Tabs (IA: Daftar Apel - Rutin, Tambahan, Khusus) -->
    <div class="flex items-center gap-2 border-b border-border pb-3 overflow-x-auto">
        <a href="{{ route('schedules.index') }}" 
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors {{ !request()->filled('category') ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-border text-text-secondary hover:text-text-primary' }}">
            <span>Semua Apel</span>
            <span class="px-1.5 py-0.2 rounded-full text-[11px] {{ !request()->filled('category') ? 'bg-white/20 text-white' : 'bg-background text-text-secondary' }}">{{ $categoryCounts['all'] }}</span>
        </a>
        <a href="{{ route('schedules.index', ['category' => 'rutin']) }}" 
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors {{ request('category') === 'rutin' ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-border text-text-secondary hover:text-text-primary' }}">
            <span>Apel Rutin</span>
            <span class="px-1.5 py-0.2 rounded-full text-[11px] {{ request('category') === 'rutin' ? 'bg-white/20 text-white' : 'bg-background text-text-secondary' }}">{{ $categoryCounts['rutin'] }}</span>
        </a>
        <a href="{{ route('schedules.index', ['category' => 'tambahan']) }}" 
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors {{ request('category') === 'tambahan' ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-border text-text-secondary hover:text-text-primary' }}">
            <span>Apel Tambahan</span>
            <span class="px-1.5 py-0.2 rounded-full text-[11px] {{ request('category') === 'tambahan' ? 'bg-white/20 text-white' : 'bg-background text-text-secondary' }}">{{ $categoryCounts['tambahan'] }}</span>
        </a>
        <a href="{{ route('schedules.index', ['category' => 'khusus']) }}" 
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium transition-colors {{ request('category') === 'khusus' ? 'bg-primary text-white shadow-xs' : 'bg-surface border border-border text-text-secondary hover:text-text-primary' }}">
            <span>Apel Khusus</span>
            <span class="px-1.5 py-0.2 rounded-full text-[11px] {{ request('category') === 'khusus' ? 'bg-white/20 text-white' : 'bg-background text-text-secondary' }}">{{ $categoryCounts['khusus'] }}</span>
        </a>
    </div>

    <!-- Schedules Table Card -->
    <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
        @if($schedules->isEmpty())
            <div class="p-12 text-center text-text-secondary text-[14px]">
                <p class="font-bold text-text-primary">Belum Ada Jadwal Apel</p>
                <p class="text-[12px] mt-0.5">Silakan tambahkan jadwal apel untuk memulai sesi absensi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-background border-b border-border text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        <tr>
                            <th class="py-3 px-4">Tanggal & Judul Apel</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Waktu Apel</th>
                            <th class="py-3 px-4">Periode Presensi</th>
                            <th class="py-3 px-4">Batas Tepat Waktu</th>
                            <th class="py-3 px-4">Total Hadir</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach($schedules as $schedule)
                            <tr class="hover:bg-background/40 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-text-primary">{{ $schedule->title }}</div>
                                    <div class="text-[11px] text-text-secondary">
                                        {{ $schedule->schedule_date->format('l, d F Y') }} &bull; {{ $schedule->location }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $catColor = match($schedule->category) {
                                            'tambahan' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'khusus' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            default => 'bg-blue-50 text-blue-700 border-blue-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $catColor }}">
                                        {{ ucfirst($schedule->category ?? 'rutin') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-text-primary">
                                    {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                </td>
                                <td class="py-3 px-4 font-mono text-text-secondary text-[12px]">
                                    {{ substr($schedule->attendance_start, 0, 5) }} - {{ substr($schedule->attendance_end, 0, 5) }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-warning">
                                    {{ substr($schedule->late_threshold_time, 0, 5) }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-mono font-bold text-text-primary">{{ $schedule->attendance_records_count }}</span>
                                    <span class="text-[11px] text-text-secondary">peserta</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $badgeColor = match($schedule->status) {
                                            'active' => 'bg-success/10 text-success',
                                            'scheduled' => 'bg-primary/10 text-primary',
                                            'completed' => 'bg-border/60 text-text-secondary',
                                            'cancelled' => 'bg-error/10 text-error',
                                            default => 'bg-background text-text-secondary'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[11px] font-bold {{ $badgeColor }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        
                                        <!-- Quick Status Toggle -->
                                        @if($schedule->status === 'scheduled')
                                            <form method="POST" action="{{ route('schedules.update-status', $schedule) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="px-2 py-1 rounded-sm bg-success hover:bg-success/90 text-white text-[11px] font-bold transition-colors shadow-xs">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @elseif($schedule->status === 'active')
                                            <form method="POST" action="{{ route('schedules.update-status', $schedule) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="px-2 py-1 rounded-sm bg-background border border-border hover:bg-border/40 text-text-secondary text-[11px] font-bold transition-colors">
                                                    Selesaikan
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Batalkan Apel (IA requirement) -->
                                        @if(!in_array($schedule->status, ['completed', 'cancelled']))
                                            <form method="POST" action="{{ route('schedules.cancel', $schedule) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan jadwal apel ini? Status akan diubah menjadi Cancelled.')">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 rounded-sm bg-error/10 hover:bg-error/20 text-error text-[11px] font-bold transition-colors inline-flex items-center gap-1" title="Batalkan Apel">
                                                    <i data-lucide="ban" class="w-3 h-3"></i>
                                                    <span>Batalkan</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Edit -->
                                        <a href="{{ route('schedules.edit', $schedule) }}" class="p-1.5 rounded-md hover:bg-background text-text-secondary hover:text-text-primary transition-colors" title="Ubah Jadwal">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <!-- Delete (Protected by FR-04) -->
                                        <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-md hover:bg-background text-text-secondary hover:text-error transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-border bg-surface">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
