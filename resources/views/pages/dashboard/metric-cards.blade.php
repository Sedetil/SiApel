<!-- FOUR METRIC CARDS (2x2 Grid) -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

    <!-- Card 1: Hadir tepat waktu -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between min-h-[145px]">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Hadir tepat waktu</span>
                <div class="w-7 h-7 rounded-lg bg-[#E6F8EF] text-[#22C55E] flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-4 h-4 text-[#22C55E]"></i>
                </div>
            </div>
            <div class="text-[28px] sm:text-[32px] font-bold text-slate-800 my-1 font-sans leading-none">
                {{ number_format($stats['session_hadir']) }} <span class="text-xs font-normal text-slate-400 ml-1">orang</span>
            </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-2">
            <strong class="text-[#22C55E] font-bold">{{ number_format($stats['hadir_rate'], 1, ',', '.') }}%</strong> Dari total Mahasiswa Anda
        </div>
    </div>

    <!-- Card 2: Hadir terlambat -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between min-h-[145px]">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Hadir terlambat</span>
                <div class="w-7 h-7 rounded-lg bg-[#FFF7ED] text-[#F97316] flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 text-[#F97316]"></i>
                </div>
            </div>
            <div class="text-[28px] sm:text-[32px] font-bold text-slate-800 my-1 font-sans leading-none">
                {{ number_format($stats['session_terlambat']) }} <span class="text-xs font-normal text-slate-400 ml-1">orang</span>
            </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-2">
            <strong class="text-[#F97316] font-bold">{{ number_format($stats['terlambat_rate'], 1, ',', '.') }}%</strong> Dari total Mahasiswa Anda
        </div>
    </div>

    <!-- Card 3: Tidak hadir -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between min-h-[145px]">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Tidak hadir</span>
                <div class="w-7 h-7 rounded-lg bg-[#FEEBED] text-[#FF4D4F] flex items-center justify-center shrink-0">
                    <i data-lucide="user-x" class="w-4 h-4 text-[#FF4D4F]"></i>
                </div>
            </div>
            <div class="text-[28px] sm:text-[32px] font-bold text-slate-800 my-1 font-sans leading-none">
                {{ number_format($stats['session_tidak_hadir']) }} <span class="text-xs font-normal text-slate-400 ml-1">orang</span>
            </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-2">
            <strong class="text-[#FF4D4F] font-bold">{{ number_format($stats['tidak_hadir_rate'], 1, ',', '.') }}%</strong> Dari Total Mahasiswa Anda
        </div>
    </div>

    <!-- Card 4: Izin & sakit -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between min-h-[145px]">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Izin & Sakit</span>
                <div class="w-7 h-7 rounded-lg bg-[#FEF4E6] text-[#F5A623] flex items-center justify-center shrink-0">
                    <i data-lucide="clipboard-check" class="w-4 h-4 text-[#F5A623]"></i>
                </div>
            </div>
            <div class="text-[28px] sm:text-[32px] font-bold text-slate-800 my-1 font-sans leading-none">
                {{ number_format($stats['session_izin'] + $stats['session_sakit']) }} <span class="text-xs font-normal text-slate-400 ml-1">orang</span>
            </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-2">
            <strong class="text-[#F5A623] font-bold">{{ number_format($stats['distribution']['izin_sakit_pct'], 1, ',', '.') }}%</strong> Dari Total Mahasiswa Anda
        </div>
    </div>

</div>
