@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Rekapitulasi & Laporan Presensi
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Laporan komprehensif kehadiran apel kedisiplinan dan ekspor data resmi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Export CSV / Excel Button -->
            <a href="{{ route('reports.export-csv', request()->query()) }}" class="inline-flex items-center gap-1.5 bg-surface border border-border hover:bg-background text-text-primary px-3.5 py-2 rounded-md text-[13px] font-medium transition-colors shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-success"></i>
                <span>Ekspor Excel (CSV)</span>
            </a>

            <!-- Print / PDF Button -->
            <a href="{{ route('reports.export-pdf', request()->query()) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-dark text-white px-3.5 py-2 rounded-md text-[13px] font-medium transition-colors shadow-sm">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <div>
                <label for="schedule_id" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Jadwal Apel</label>
                <select id="schedule_id" name="schedule_id" class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:border-primary">
                    <option value="">Semua Sesi Apel</option>
                    @foreach($schedules as $s)
                        <option value="{{ $s->id }}" {{ $selectedScheduleId == $s->id ? 'selected' : '' }}>
                            {{ $s->schedule_date->format('d/m/Y') }} — {{ $s->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Status Kehadiran</label>
                <select id="status" name="status" class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:border-primary">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ $selectedStatus === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                    <option value="terlambat" {{ $selectedStatus === 'terlambat' ? 'selected' : '' }}>Terlambat Apel</option>
                </select>
            </div>

            <div>
                <label for="study_program" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Program Studi</label>
                <select id="study_program" name="study_program" class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:border-primary">
                    <option value="">Semua Program Studi</option>
                    @foreach($studyPrograms as $prodi)
                        <option value="{{ $prodi }}" {{ $selectedProdi === $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Tanggal</label>
                <input type="date" id="date" name="date" value="{{ $selectedDate }}" class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:border-primary">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white text-[13px] font-medium py-2 px-3 rounded-md transition-colors shadow-sm flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('reports.index') }}" class="bg-background hover:bg-border/40 text-text-secondary text-[13px] py-2 px-3 rounded-md transition-colors border border-border">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-text-secondary block mb-1">Total Tercatat Sesuai Filter</span>
            <div class="font-mono text-[32px] font-bold text-text-primary">{{ $stats['total_recorded'] }}</div>
        </div>
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-success block mb-1">Hadir Tepat Waktu</span>
            <div class="font-mono text-[32px] font-bold text-success">{{ $stats['total_hadir'] }}</div>
        </div>
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-warning block mb-1">Terlambat Apel</span>
            <div class="font-mono text-[32px] font-bold text-warning">{{ $stats['total_terlambat'] }}</div>
        </div>
    </div>

    <!-- Report Preview Table Card -->
    <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b border-border flex items-center justify-between">
            <h3 class="text-[14px] font-bold text-text-primary">Hasil Rekapitulasi Presensi</h3>
            <span class="text-[12px] text-text-secondary">Menampilkan {{ $records->total() }} catatan</span>
        </div>

        @if($records->isEmpty())
            <div class="p-12 text-center text-text-secondary text-[14px]">
                <p class="font-bold text-text-primary">Tidak Ada Data</p>
                <p class="text-[12px] mt-0.5">Silakan sesuaikan kriteria filter laporan di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-background border-b border-border text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">NPM / NIM</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Program Studi</th>
                            <th class="py-3 px-4">Kompi / Kelas</th>
                            <th class="py-3 px-4">Sesi Apel</th>
                            <th class="py-3 px-4">Waktu Masuk</th>
                            <th class="py-3 px-4">Waktu Keluar</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach($records as $index => $record)
                            <tr class="hover:bg-background/40 transition-colors">
                                <td class="py-3 px-4 font-mono text-[12px] text-text-secondary">
                                    {{ $records->firstItem() + $index }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-text-primary">
                                    {{ $record->participant?->identifier }}
                                </td>
                                <td class="py-3 px-4 font-bold text-text-primary">
                                    @if($record->participant_id)
                                        <a href="{{ route('participants.show', $record->participant_id) }}" class="hover:text-primary transition-colors inline-flex items-center gap-1 group">
                                            <span>{{ $record->participant?->name }}</span>
                                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-text-secondary group-hover:text-primary transition-colors shrink-0"></i>
                                        </a>
                                    @else
                                        {{ $record->participant?->name }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-text-secondary">
                                    {{ $record->participant?->study_program ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-text-secondary text-[12px]">
                                    {{ $record->participant?->class_group ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-text-secondary text-[12px]">
                                    <div>{{ $record->schedule?->title }}</div>
                                    <div class="text-[11px]">{{ $record->schedule?->schedule_date->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-text-primary">
                                    {{ $record->check_in_time ? $record->check_in_time->format('H:i:s') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-text-secondary">
                                    {{ $record->check_out_time ? $record->check_out_time->format('H:i:s') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($record->status === 'hadir')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-success/10 text-success">
                                            Hadir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-warning/10 text-warning">
                                            Terlambat
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-border bg-surface">
                {{ $records->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
