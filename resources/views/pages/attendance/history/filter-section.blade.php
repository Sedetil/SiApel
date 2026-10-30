<!-- SEARCH & FILTER SECTION (CARD PUTIH, LEBAR PENUH) -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-5">
    
    <form method="GET" action="{{ route('attendance.history') }}" class="space-y-4" id="attendance-filter-form">
        
        <!-- Baris Atas: Input Forms (Tanggal Apel, Pencarian Mahasiswa, Jurusan, Program Studi, Tombol Cari) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            
            <!-- Field 1: Tanggal Apel (Hari dan Tanggal Mengikuti Jadwal yang Ada) -->
            <div class="lg:col-span-3">
                <label for="date-select" class="block text-[12px] font-bold text-slate-700 mb-1.5">
                    Tanggal Apel
                </label>
                <div class="relative">
                    <select 
                        id="date-select" 
                        name="date" 
                        class="w-full bg-[#f8fafc] border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all font-medium truncate"
                    >
                        <option value="">Semua Jadwal Apel</option>
                        @foreach($availableDates as $d)
                            @php
                                $dCarbon = \Carbon\Carbon::parse($d)->locale('id');
                                $displayDate = $dCarbon->translatedFormat('l, d F Y');
                                $valDate = $dCarbon->format('d/m/Y');
                                $isSelected = (request('date') === $valDate || request('date') === $d || ($selectedDate && $selectedDate === $d));
                            @endphp
                            <option value="{{ $valDate }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $displayDate }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <!-- Field 2: Pencarian Mahasiswa -->
            <div class="lg:col-span-3">
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
                        placeholder="Ketik nama atau NPM..."
                        class="w-full bg-[#f8fafc] border border-slate-200 text-slate-800 text-xs rounded-xl pl-10 pr-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all placeholder:text-slate-400 font-medium"
                    >
                </div>
            </div>

            <!-- Field 3: Jurusan -->
            <div class="lg:col-span-3">
                <label for="department-select" class="block text-[12px] font-bold text-slate-700 mb-1.5">
                    Jurusan
                </label>
                <div class="relative">
                    <select 
                        id="department-select" 
                        name="department" 
                        class="w-full bg-[#f8fafc] border border-slate-200 text-slate-700 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all font-medium truncate"
                    >
                        <option value="">Semua Jurusan</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department', auth()->user()?->department) === $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <!-- Field 4: Program Studi -->
            <div class="lg:col-span-2">
                <label for="study-program-select" class="block text-[12px] font-bold text-slate-700 mb-1.5">
                    Program Studi
                </label>
                <div class="relative">
                    <select 
                        id="study-program-select" 
                        name="study_program" 
                        class="w-full bg-[#f8fafc] border border-slate-200 text-slate-700 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all font-medium truncate"
                    >
                        <option value="">Semua Program Studi</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp }}" {{ request('study_program', auth()->user()?->study_program) === $sp ? 'selected' : '' }}>
                                {{ $sp }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <!-- Tombol Cari -->
            <div class="lg:col-span-1">
                <button 
                    type="submit" 
                    class="w-full bg-[#2D56FF] hover:bg-blue-700 text-white font-semibold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer h-[39px]"
                >
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Cari</span>
                </button>
            </div>

        </div>

        @if(request()->filled('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <!-- Baris Bawah: Filter Status Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-2">
            <span class="text-[12px] font-bold text-slate-700 mr-1 select-none">
                Filter Status:
            </span>

            @php
                $currentStatus = request('status');
                $isAll = empty($currentStatus);
                $counts = $summaryStats['counts'] ?? [
                    'semua' => 0,
                    'hadir' => 0,
                    'terlambat' => 0,
                    'sakit' => 0,
                    'tidak_hadir' => 0,
                ];
            @endphp

            <!-- Semua (Biru Solid saat aktif) -->
            <a href="{{ route('attendance.history', array_merge(request()->except('status', 'page'))) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $isAll ? 'bg-[#2D56FF] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                Semua <span class="{{ $isAll ? 'text-blue-100' : 'text-slate-500' }} ml-1 font-medium">{{ number_format($counts['semua']) }}</span>
            </a>

            <!-- Hadir Tepat (Dot Hijau) -->
            <a href="{{ route('attendance.history', array_merge(request()->except('page'), ['status' => 'hadir'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ $currentStatus === 'hadir' ? 'bg-[#ecfdf5] text-[#16a34a] border border-emerald-300 font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                <span class="w-2 h-2 rounded-full bg-[#16a34a] shrink-0"></span>
                <span>Hadir Tepat</span>
                <span class="{{ $currentStatus === 'hadir' ? 'text-[#16a34a]' : 'text-slate-500' }} ml-0.5">{{ number_format($counts['hadir']) }}</span>
            </a>

            <!-- Terlambat (Dot Merah) -->
            <a href="{{ route('attendance.history', array_merge(request()->except('page'), ['status' => 'terlambat'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ $currentStatus === 'terlambat' ? 'bg-[#fef2f2] text-[#ef4444] border border-rose-300 font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                <span class="w-2 h-2 rounded-full bg-[#ef4444] shrink-0"></span>
                <span>Terlambat</span>
                <span class="{{ $currentStatus === 'terlambat' ? 'text-[#ef4444]' : 'text-rose-500' }} ml-0.5 font-medium">{{ number_format($counts['terlambat']) }}</span>
            </a>

            <!-- Sakit (Dot Oranye / Kuning) -->
            <a href="{{ route('attendance.history', array_merge(request()->except('page'), ['status' => 'sakit'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ $currentStatus === 'sakit' ? 'bg-[#fffbeb] text-[#d97706] border border-amber-300 font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                <span class="w-2 h-2 rounded-full bg-[#f59e0b] shrink-0"></span>
                <span>Sakit</span>
                <span class="{{ $currentStatus === 'sakit' ? 'text-[#d97706]' : 'text-slate-500' }} ml-0.5">{{ number_format($counts['sakit']) }}</span>
            </a>

            <!-- Tidak Hadir (Dot Hitam) -->
            <a href="{{ route('attendance.history', array_merge(request()->except('page'), ['status' => 'tidak_hadir'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ in_array($currentStatus, ['alpa', 'tidak_hadir']) ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                <span class="w-2 h-2 rounded-full {{ in_array($currentStatus, ['alpa', 'tidak_hadir']) ? 'bg-white' : 'bg-slate-800' }} shrink-0"></span>
                <span>Tidak Hadir</span>
                <span class="{{ in_array($currentStatus, ['alpa', 'tidak_hadir']) ? 'text-slate-300' : 'text-slate-500' }} ml-0.5 font-medium">{{ number_format($counts['tidak_hadir']) }}</span>
            </a>

        </div>

    </form>

    <!-- Teks Informasi Ditemukan -->
    <div class="text-[12px] text-slate-500 pt-3 border-t border-slate-100 flex items-center justify-between">
        <div>
            Ditemukan: <strong class="text-slate-800 font-bold">{{ number_format($records->total()) }} data</strong> mahasiswa
        </div>
        @if(request()->hasAny(['search', 'status', 'department', 'study_program', 'class_group', 'date']))
            <a href="{{ route('attendance.history') }}" class="text-[11.5px] text-[#2D56FF] hover:underline font-semibold flex items-center gap-1">
                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                <span>Reset filter</span>
            </a>
        @endif
    </div>

</div>
