<!-- BARIS PENCARIAN & FILTER DROPDOWNS -->
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
    
    <!-- Input Pencarian (Lebar & Dominan) -->
    <div class="relative flex-1">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        <input 
            type="text" 
            id="searchOfficerInput" 
            oninput="filterOfficersTable()"
            placeholder="Cari nama atau username anggota..." 
            class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs"
        >
    </div>

    <!-- Dropdown 1: Semua peran -->
    <div class="relative min-w-[170px]">
        <select 
            id="filterRole" 
            onchange="filterOfficersTable()"
            class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
        >
            <option value="">Semua peran</option>
            <option value="Koordinator">Koordinator</option>
            <option value="Anggota">Anggota</option>
            <option value="Sekretaris">Sekretaris</option>
        </select>
        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
    </div>

    <!-- Dropdown 2: Semua penugasan -->
    <div class="relative min-w-[185px]">
        <select 
            id="filterAssignment" 
            onchange="filterOfficersTable()"
            class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
        >
            <option value="">Semua penugasan</option>
            <option value="Lapangan GSG">Lapangan GSG</option>
            <option value="Lapangan GKB">Lapangan GKB</option>
            <option value="Gerbang Utama">Gerbang Utama</option>
            <option value="Parkir Terpadu">Parkir Terpadu</option>
        </select>
        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
    </div>

    <!-- Dropdown 3: Semua status -->
    <div class="relative min-w-[140px]">
        <select 
            id="filterStatus" 
            onchange="filterOfficersTable()"
            class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
        >
            <option value="">Semua status</option>
            <option value="Aktif">Aktif</option>
            <option value="Nonaktif">Nonaktif</option>
        </select>
        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
    </div>

</div>
