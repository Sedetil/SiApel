<!-- SEARCH & FILTER SECTION (CARD PUTIH, LEBAR PENUH) -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-5">
    
    <form method="GET" action="{{ role_route('attendance.history') }}" class="space-y-4" id="attendance-filter-form">
        
        <!-- Baris Atas: Input Forms (Grid/Flex) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            
            <!-- Field 1: Tanggal Apel -->
            <div class="lg:col-span-3">
                <label for="date-input" class="block text-[12px] font-bold text-slate-700 mb-1.5">
                    Tanggal Apel
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="date-input" 
                        name="date" 
                        value="{{ request('date', '09/09/2026') }}" 
                        placeholder="09/09/2026"
                        class="w-full bg-[#f8fafc] border border-slate-200/90 text-slate-800 text-[13px] rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] transition-all placeholder:text-slate-400"
                    >
                </div>
            </div>

            <!-- Field 2: Pencarian Mahasiswa -->
            <div class="lg:col-span-4">
                <label for="search-input" class="block text-[12px] font-bold text-slate-700 mb-1.5">
                    Pencarian Mahasiswa
                </label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="text" 
                        id="search-input" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Ketik nama atau NPM mahasiswa..."
                        class="w-full bg-[#f8fafc] border border-slate-200/90 text-slate-800 text-[13px] rounded-xl pl-10 pr-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] transition-all placeholder:text-slate-400"
                    >
                </div>
            </div>

            <!-- Field 3: Jurusan / Program Studi -->
            <div class="lg:col-span-3">
                <label for="study-program-select" class="block text-[12px] font-bold text-slate-700 mb-1.5">
                    Jurusan / Program Studi
                </label>
                <div class="relative">
                    <select 
                        id="study-program-select" 
                        name="study_program" 
                        class="w-full bg-[#f8fafc] border border-slate-200/90 text-slate-800 text-[13px] rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] transition-all appearance-none cursor-pointer pr-10"
                    >
                        <option value="Teknologi Informasi" {{ request('study_program', 'Teknologi Informasi') === 'Teknologi Informasi' ? 'selected' : '' }}>
                            Teknologi Informasi
                        </option>
                        <option value="Manajemen Informatika" {{ request('study_program') === 'Manajemen Informatika' ? 'selected' : '' }}>
                            Manajemen Informatika
                        </option>
                        <option value="Teknologi Rekayasa Perangkat Lunak" {{ request('study_program') === 'Teknologi Rekayasa Perangkat Lunak' ? 'selected' : '' }}>
                            Teknologi Rekayasa Perangkat Lunak
                        </option>
                        <option value="Teknologi Rekayasa Internet" {{ request('study_program') === 'Teknologi Rekayasa Internet' ? 'selected' : '' }}>
                            Teknologi Rekayasa Internet
                        </option>
                        <option value="Agribisnis Pangan" {{ request('study_program') === 'Agribisnis Pangan' ? 'selected' : '' }}>
                            Agribisnis Pangan
                        </option>
                        <option value="Akuntansi Perpajakan" {{ request('study_program') === 'Akuntansi Perpajakan' ? 'selected' : '' }}>
                            Akuntansi Perpajakan
                        </option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <!-- Tombol Cari -->
            <div class="lg:col-span-2">
                <button 
                    type="submit" 
                    class="w-full bg-[#2563eb] hover:bg-blue-700 text-white font-medium text-[13px] py-2.5 px-4 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer h-[42px]"
                >
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari</span>
                </button>
            </div>

        </div>

        <!-- Baris Bawah: Filter Status -->
        <div class="flex flex-wrap items-center gap-2.5 pt-1">
            <span class="text-[12px] font-bold text-slate-600 mr-1 select-none">
                Filter Status:
            </span>

            @php
                $currentStatus = request('status');
                $isAll = empty($currentStatus);
            @endphp

            <!-- Semua 3520 (Biru Aktif) -->
            <a href="{{ role_route('attendance.history', array_merge(request()->except('status', 'page'))) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $isAll ? 'bg-[#2563eb] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                Semua <span class="{{ $isAll ? 'text-blue-100' : 'text-slate-500' }} ml-0.5">3520</span>
            </a>

            <!-- Hadir Tepat 436 (Dot Hijau) -->
            <a href="{{ role_route('attendance.history', array_merge(request()->except('page'), ['status' => 'hadir'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ $currentStatus === 'hadir' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                <span>Hadir Tepat</span>
                <span class="text-slate-500 font-normal">436</span>
            </a>

            <!-- Terlambat 32 (Dot Merah) -->
            <a href="{{ role_route('attendance.history', array_merge(request()->except('page'), ['status' => 'terlambat'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ $currentStatus === 'terlambat' ? 'bg-red-50 text-red-700 border border-red-200 font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                <span>Terlambat</span>
                <span class="{{ $currentStatus === 'terlambat' ? 'text-red-500 font-bold' : 'text-red-500' }}">32</span>
            </a>

            <!-- Sakit 4 (Dot Kuning) -->
            <a href="{{ role_route('attendance.history', array_merge(request()->except('page'), ['status' => 'sakit'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ $currentStatus === 'sakit' ? 'bg-amber-50 text-amber-700 border border-amber-200 font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                <span>Sakit</span>
                <span class="text-slate-500 font-normal">4</span>
            </a>

            <!-- Tidak Hadir 8 (Dot Hitam/Gelap) -->
            <a href="{{ role_route('attendance.history', array_merge(request()->except('page'), ['status' => 'alpa'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ in_array($currentStatus, ['alpa', 'tidak_hadir']) ? 'bg-slate-800 text-white font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span class="w-2 h-2 rounded-full bg-slate-800 shrink-0"></span>
                <span>Tidak Hadir</span>
                <span class="{{ in_array($currentStatus, ['alpa', 'tidak_hadir']) ? 'text-slate-300' : 'text-slate-500' }} font-normal">8</span>
            </a>

        </div>

    </form>

    <!-- Teks Informasi Data -->
    <div class="text-[12px] text-slate-500 pt-1 border-t border-slate-100/80">
        Ditemukan: <strong class="text-slate-700 font-bold">3520 data</strong> mahasiswa
    </div>

</div>
