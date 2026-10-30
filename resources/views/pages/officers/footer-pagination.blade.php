<!-- 5. FOOTER CARD: INFO JUMLAH, DROPDOWN PER HALAMAN & PAGINATION -->
<div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
    
    <!-- Kiri: Menampilkan Data & Dropdown Halaman -->
    <div class="flex items-center gap-3 text-[13px] text-slate-500">
        <span class="font-normal">
            Menampilkan <span id="officerVisibleRange">1–10</span> dari 24 data
        </span>
        
        <div class="relative">
            <select class="appearance-none bg-white border border-slate-200/90 rounded-xl px-3 py-1.5 pr-7 text-[12.5px] font-medium text-slate-700 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-600/20 cursor-pointer">
                <option value="10">10 / halaman</option>
                <option value="25">25 / halaman</option>
                <option value="50">50 / halaman</option>
            </select>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>
    </div>

    <!-- Kanan: Kontrol Pagination -->
    <div class="flex items-center gap-1 select-none">
        <!-- Prev Button -->
        <button 
            type="button" 
            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-600 cursor-pointer transition-colors"
            title="Halaman Sebelumnya"
        >
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>

        <!-- Page 1 (Aktif Solid Biru) -->
        <span class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold text-[13px] flex items-center justify-center shadow-xs">
            1
        </span>

        <!-- Page 2 -->
        <button 
            type="button" 
            class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-[13px] flex items-center justify-center transition-colors cursor-pointer"
        >
            2
        </button>

        <!-- Page 3 -->
        <button 
            type="button" 
            class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-[13px] flex items-center justify-center transition-colors cursor-pointer"
        >
            3
        </button>

        <!-- Next Button -->
        <button 
            type="button" 
            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 cursor-pointer transition-colors"
            title="Halaman Berikutnya"
        >
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>
    </div>

</div>
