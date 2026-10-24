<!-- THREE STATS CARDS ROW (Grid 2.1fr 1fr 1fr) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[2.1fr_1fr_1fr] gap-4">
    
    <!-- Card 1 (Blue Gradient Solid): TOTAL MAHASISWA POLINELA -->
    <div class="rounded-xl p-6 text-white flex items-center justify-between relative overflow-hidden shadow-[0_2px_8px_rgba(13,71,161,0.18)]" style="background: linear-gradient(135deg, #092c74 0%, #0d47a1 45%, #1976d2 75%, #0a358c 100%); min-height: 148px;">
        
        <div class="z-10 flex flex-col justify-between h-full">
            <div>
                <span class="text-[11px] font-semibold text-blue-100 uppercase tracking-wider opacity-90 block">
                    TOTAL MAHASISWA POLINELA
                </span>
                <div class="text-[32px] font-bold tracking-tight my-2 font-sans">
                    {{ number_format($stats['total_participants']) }}
                </div>
            </div>
            <span class="text-[11px] font-semibold text-blue-100 uppercase tracking-wide opacity-90 block">
                AKTIF TERDAFTAR DALAM SISTEM
            </span>
        </div>

        <!-- Illustration inside blue card -->
        <div class="shrink-0 pointer-events-none pr-1 flex items-center justify-center">
            <img src="{{ asset('static/img/icon1.png') }}" alt="Ilustrasi Mahasiswa" class="h-24 sm:h-28 object-contain drop-shadow-sm">
        </div>

    </div>

    <!-- Card 2: Hadir tepat waktu -->
    <div class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Hadir tepat waktu</span>
                <div class="w-7 h-7 rounded-md bg-[#dcfce7] text-[#16a34a] flex items-center justify-center">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-[26px] font-bold text-slate-800 my-1 font-sans">
                {{ number_format($stats['session_hadir']) }} <small class="text-xs font-normal text-slate-500">orang</small>
            </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-3">
            <strong class="text-[#16a34a] font-bold">{{ number_format($stats['hadir_rate'], 1, ',', '.') }}%</strong> Dari total Mahasiswa
        </div>
    </div>

    <!-- Card 3: Tidak hadir -->
    <div class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-medium">Tidak hadir</span>
                <div class="w-7 h-7 rounded-md bg-[#fee2e2] text-[#dc2626] flex items-center justify-center">
                    <i data-lucide="user-x" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-[26px] font-bold text-slate-800 my-1 font-sans">
                {{ number_format($stats['session_tidak_hadir']) }} <small class="text-xs font-normal text-slate-500">orang</small>
            </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-3">
            <strong class="text-[#dc2626] font-bold">{{ number_format($stats['tidak_hadir_rate'], 1, ',', '.') }}%</strong> Dari Total Mahasiswa
        </div>
    </div>

</div>
