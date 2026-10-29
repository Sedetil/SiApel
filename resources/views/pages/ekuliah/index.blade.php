@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Integrasi Sistem E-Kuliah POLINELA
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Konfigurasi jembatan data antara SiApel dengan portal akademik E-Kuliah untuk sinkronisasi mahasiswa dan presensi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Koneksi API Aktif</span>
            </span>
        </div>
    </div>

    <!-- Alert Feedback -->
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-medium flex items-center gap-2.5 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Mahasiswa Tersinkron</span>
            <div class="text-[26px] font-bold text-slate-800 font-sans">{{ number_format($stats['total_students']) }}</div>
            <span class="text-[11px] text-emerald-600 font-medium">Data aktif dalam sistem</span>
        </div>

        <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Presensi Terkirim</span>
            <div class="text-[26px] font-bold text-blue-600 font-sans">{{ number_format($stats['total_synced_attendance']) }}</div>
            <span class="text-[11px] text-blue-600 font-medium">Tercatat ke E-Kuliah</span>
        </div>

        <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Sinkronisasi</span>
            <div class="text-[26px] font-bold text-emerald-600 flex items-center gap-1.5">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                <span>Normal</span>
            </div>
            <span class="text-[11px] text-slate-400 font-mono">{{ $config['last_synced'] }}</span>
        </div>

        <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Riwayat Log Tercatat</span>
            <div class="text-[26px] font-bold text-slate-800 font-sans">{{ $stats['total_logs'] }}</div>
            <span class="text-[11px] text-slate-400">Total aktivitas sinkron</span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Konfigurasi & Tombol Aksi (5 cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 1: Konfigurasi API -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Konfigurasi API E-Kuliah</h2>
                        <p class="text-[12px] text-slate-500 mt-0.5">Endpoint server dan kredensial autentikasi sistem.</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.ekuliah.update-config') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="ekuliah_url" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            URL Endpoint API E-Kuliah <span class="text-red-500">*</span>
                        </label>
                        <input type="url" id="ekuliah_url" name="ekuliah_url" value="{{ old('ekuliah_url', $config['url']) }}" required
                               class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div>
                        <label for="ekuliah_api_key" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            API Secret Key <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="ekuliah_api_key" name="ekuliah_api_key" value="{{ old('ekuliah_api_key', $config['api_key']) }}" required
                               class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="ekuliah_auto_sync" value="1" {{ $config['auto_sync'] ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-primary border-slate-300 focus:ring-primary">
                            <span class="text-[12.5px] text-slate-700 font-medium">Otomatis sinkronisasi terjadwal setiap 6 jam</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-semibold text-[13px] px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Konfigurasi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Aksi Sinkronisasi Manual -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Aksi Sinkronisasi Data</h2>
                        <p class="text-[12px] text-slate-500 mt-0.5">Jalankan proses transfer data secara manual kapan saja.</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Action: Sinkronisasi data (Mahasiswa, kelas, jadwal) -->
                    <form method="POST" action="{{ route('admin.ekuliah.sync-data') }}">
                        @csrf
                        <button type="submit" class="w-full p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 hover:bg-blue-50 text-left transition-colors flex items-start justify-between group">
                            <div>
                                <div class="font-bold text-blue-900 text-[13px] group-hover:text-primary transition-colors flex items-center gap-1.5">
                                    <i data-lucide="download-cloud" class="w-4 h-4 text-blue-600"></i>
                                    <span>Tarik Data Mahasiswa & Jadwal</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Memperbarui master mahasiswa, kelas, dan jadwal semester dari server E-Kuliah.
                                </p>
                            </div>
                            <span class="px-2 py-1 bg-white border border-blue-200 text-blue-700 font-bold text-[11px] rounded-md shadow-2xs shrink-0 ml-2">
                                Tarik Data
                            </span>
                        </button>
                    </form>

                    <!-- Action: Kirim kehadiran (Presensi yang belum tersinkron) -->
                    <form method="POST" action="{{ route('admin.ekuliah.sync-attendance') }}">
                        @csrf
                        <button type="submit" class="w-full p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 text-left transition-colors flex items-start justify-between group">
                            <div>
                                <div class="font-bold text-emerald-900 text-[13px] group-hover:text-emerald-700 transition-colors flex items-center gap-1.5">
                                    <i data-lucide="upload-cloud" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Kirim Presensi Kehadiran</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Mengunggah seluruh catatan kehadiran apel mahasiswa yang belum tersinkron ke nilai kedisiplinan.
                                </p>
                            </div>
                            <span class="px-2 py-1 bg-white border border-emerald-200 text-emerald-700 font-bold text-[11px] rounded-md shadow-2xs shrink-0 ml-2">
                                Kirim Presensi
                            </span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Column: Status dan Log Riwayat Sinkronisasi (7 cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Status & Log Sinkronisasi</h2>
                        <p class="text-[12px] text-slate-500 mt-0.5">Rekam jejak seluruh transmisi data API E-Kuliah</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-white border border-slate-200 text-slate-600">
                        {{ $logs->total() }} Log
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px]">
                        <thead class="bg-slate-50/70 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Jenis Transmisi</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4 text-center">Data</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 px-4 font-mono text-[11.5px] text-slate-500 whitespace-nowrap">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-800 capitalize">{{ $log->sync_type }}</span>
                                        <div class="text-[10px] text-slate-400">{{ $log->user?->name ?? 'Otomatis' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-[12px] text-slate-700">
                                        {{ $log->message }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">
                                        {{ $log->records_synced }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($log->status === 'success')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i data-lucide="check" class="w-3 h-3"></i> SUKSES
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i> GAGAL
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400 italic">
                                        Belum ada riwayat aktivitas sinkronisasi yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $logs->links() }}
            </div>
        </div>

    </div>

</div>
@endsection
