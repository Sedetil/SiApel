<!-- SUMMARY STATS CARDS (GRID 1x4) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- Card 1: Hadir Hari Ini -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-5 flex items-start justify-between relative overflow-hidden transition-all hover:shadow-md">
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                HADIR HARI INI
            </div>
            <div class="flex items-center gap-2.5 my-1">
                <span class="text-[32px] font-bold text-slate-900 tracking-tight leading-none">
                    {{ number_format($summaryStats['hadir']['count'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#ecfdf5] text-[#16a34a]">
                    {{ $summaryStats['hadir']['pct'] ?? 0 }}%
                </span>
            </div>
            <div class="text-[11.5px] text-slate-400 font-medium mt-1.5">
                Dari total {{ number_format($summaryStats['total_target'] ?? 0) }} target peserta
            </div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-[#ecfdf5] text-[#16a34a] flex items-center justify-center shrink-0">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
    </div>

    <!-- Card 2: Terlambat Masuk -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-5 flex items-start justify-between relative overflow-hidden transition-all hover:shadow-md">
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                TERLAMBAT MASUK
            </div>
            <div class="flex items-center gap-2.5 my-1">
                <span class="text-[32px] font-bold text-slate-900 tracking-tight leading-none">
                    {{ number_format($summaryStats['terlambat']['count'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#fef2f2] text-[#ef4444]">
                    {{ $summaryStats['terlambat']['pct'] ?? 0 }}%
                </span>
            </div>
            <div class="text-[11.5px] text-slate-400 font-medium mt-1.5">
                Masuk lewat dari toleransi
            </div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-[#fef2f2] text-[#ef4444] flex items-center justify-center shrink-0">
            <i data-lucide="clock" class="w-5 h-5"></i>
        </div>
    </div>

    <!-- Card 3: Izin & Sakit -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-5 flex items-start justify-between relative overflow-hidden transition-all hover:shadow-md">
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                IZIN & SAKIT
            </div>
            <div class="flex items-center gap-2.5 my-1">
                <span class="text-[32px] font-bold text-slate-900 tracking-tight leading-none">
                    {{ number_format($summaryStats['sakit_izin']['count'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#fffbeb] text-[#d97706]">
                    {{ $summaryStats['sakit_izin']['pct'] ?? 0 }}%
                </span>
            </div>
            <div class="text-[11.5px] text-slate-400 font-medium mt-1.5">
                Dengan surat keterangan resmi
            </div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-[#fffbeb] text-[#d97706] flex items-center justify-center shrink-0">
            <i data-lucide="file-text" class="w-5 h-5"></i>
        </div>
    </div>

    <!-- Card 4: Tanpa Keterangan / Alpha -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-5 flex items-start justify-between relative overflow-hidden transition-all hover:shadow-md">
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                TANPA KETERANGAN / ALPHA
            </div>
            <div class="flex items-center gap-2.5 my-1">
                <span class="text-[32px] font-bold text-slate-900 tracking-tight leading-none">
                    {{ number_format($summaryStats['alpha']['count'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                    {{ $summaryStats['alpha']['pct'] ?? 0 }}%
                </span>
            </div>
            <div class="text-[11.5px] text-slate-400 font-medium mt-1.5">
                Belum melakukan tap kartu/QR
            </div>
        </div>
        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
        </div>
    </div>

</div>
