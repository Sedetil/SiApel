<!-- 6. PAGINATION (BAWAH TENGAH) -->
<div class="flex items-center justify-center gap-1.5 pt-2 pb-6 select-none overflow-x-auto">
    
    <!-- Tombol Prev (<) -->
    <a href="#" class="w-9 h-9 rounded-lg border border-slate-200/90 flex items-center justify-center text-slate-600 bg-white hover:bg-slate-50 transition shadow-[0_1px_2px_rgba(0,0,0,0.03)] cursor-pointer" aria-label="Previous Page" title="Halaman Sebelumnya">
        <i data-lucide="chevron-left" class="w-4 h-4"></i>
    </a>

    <!-- Angka 1 (Aktif) -->
    <a href="#" class="w-9 h-9 rounded-lg border border-slate-300 font-bold text-slate-900 bg-slate-50 flex items-center justify-center text-xs transition shadow-[0_1px_2px_rgba(0,0,0,0.03)] cursor-pointer" aria-current="page">
        1
    </a>

    <!-- Angka 2 sampai 10 -->
    @foreach(range(2, 10) as $pageNum)
        <a href="#" class="w-9 h-9 rounded-lg border border-slate-200/90 font-semibold text-slate-700 bg-white hover:bg-slate-50 flex items-center justify-center text-xs transition shadow-[0_1px_2px_rgba(0,0,0,0.03)] cursor-pointer">
            {{ $pageNum }}
        </a>
    @endforeach

    <!-- Ellipsis (...) -->
    <span class="w-9 h-9 flex items-center justify-center text-xs font-bold text-slate-400">
        ...
    </span>

    <!-- Halaman Akhir: 519 & 520 -->
    <a href="#" class="w-9 h-9 rounded-lg border border-slate-200/90 font-semibold text-slate-700 bg-white hover:bg-slate-50 flex items-center justify-center text-xs transition shadow-[0_1px_2px_rgba(0,0,0,0.03)] cursor-pointer">
        519
    </a>
    <a href="#" class="w-9 h-9 rounded-lg border border-slate-200/90 font-semibold text-slate-700 bg-white hover:bg-slate-50 flex items-center justify-center text-xs transition shadow-[0_1px_2px_rgba(0,0,0,0.03)] cursor-pointer">
        520
    </a>

    <!-- Tombol Next (>) -->
    <a href="#" class="w-9 h-9 rounded-lg border border-slate-200/90 flex items-center justify-center text-slate-600 bg-white hover:bg-slate-50 transition shadow-[0_1px_2px_rgba(0,0,0,0.03)] cursor-pointer" aria-label="Next Page" title="Halaman Berikutnya">
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
    </a>

</div>
