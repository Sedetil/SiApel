<!-- 3. BANNER PERINGATAN (ALERT BOX) -->
<div class="bg-[#fffbeb] border border-amber-200/80 rounded-xl p-3.5 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
    
    <!-- Bagian Kiri: Ikon Jam & Teks Utama -->
    <div class="flex items-center gap-2.5">
        <i data-lucide="clock" class="w-4 h-4 text-[#d97706] shrink-0"></i>
        <div class="text-[12.5px] leading-tight">
            <strong class="font-bold text-[#b45309]">
                {{ ($stats['menunggu'] ?? 0) > 0 ? $stats['menunggu'] : 8 }} pengajuan menunggu persetujuan
            </strong>
            <span class="text-[#b45309]/90 ml-1">
                &middot; Prioritaskan pengajuan dengan tanggal izin terdekat.
            </span>
        </div>
    </div>

    <!-- Bagian Kanan: Status Update & Refresh -->
    <div class="flex items-center gap-2 text-[11.5px] text-slate-500 shrink-0">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
        <span>Diperbarui pukul {{ now()->format('H.i') }} WIB</span>
        <button type="button" onclick="window.location.reload()" title="Segarkan Data" class="text-slate-400 hover:text-slate-600 transition-colors p-0.5 cursor-pointer">
            <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
        </button>
    </div>

</div>
