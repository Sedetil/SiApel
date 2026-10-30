@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- PAGE TITLE & SUBTITLE -->
    <div class="page-header">
        <h1 class="text-[24px] font-bold text-slate-900 tracking-tight leading-tight">
            {{ auth()->check() && !auth()->user()->isAdmin() ? 'Mahasiswa Saya' : 'Data Mahasiswa' }}
        </h1>
        <p class="text-[13px] text-slate-500 mt-1">
            {{ auth()->check() && !auth()->user()->isAdmin() 
                ? 'Menampilkan Daftar Informasi Tentang Mahasiswa Anda' 
                : 'Menampilkan Daftar Informasi Lengkap Mahasiswa Polinela' }}
        </p>
    </div>

    <!-- MAIN CONTAINER CARD -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-[0_1px_3px_rgba(15,23,42,0.03)] overflow-hidden">
        
        <!-- CARD HEADER: Icon + Title + Total Badge + Ekspor Button -->
        <div class="p-5 sm:p-6 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100">
            <div class="flex items-start gap-3.5">
                <!-- Blue Icon Container -->
                <div class="w-10 h-10 rounded-xl bg-[#EEF2FF] border border-[#E0E7FF] text-[#2D56FF] flex items-center justify-center shrink-0">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-[#2D56FF]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-[16px] font-bold text-slate-900 tracking-tight leading-tight">
                            Daftar mahasiswa
                        </h2>
                        <span class="text-[11.5px] font-bold text-[#2D56FF] bg-[#EEF2FF] px-2.5 py-0.5 rounded-full">
                            {{ number_format($totalCount ?? $participants->total()) }} mahasiswa
                        </span>
                    </div>
                    <p class="text-[12.5px] text-slate-400 mt-0.5">
                        Lihat, dan perbarui data mahasiswa yang terdaftar.
                    </p>
                </div>
            </div>

            <!-- Actions: Ekspor Data & Tambah (Admin) -->
            <div class="flex items-center gap-2.5 shrink-0">
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('participants.create') }}" class="inline-flex items-center gap-1.5 bg-[#2D56FF] hover:bg-blue-700 text-white px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors shadow-xs">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Mahasiswa</span>
                    </a>
                @endif

                <a 
                    href="{{ route('reports.export-pdf', ['type' => 'mahasiswa']) }}" 
                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors shadow-xs"
                    title="Ekspor Data Mahasiswa"
                >
                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Ekspor data</span>
                </a>
            </div>
        </div>

        <!-- SEARCH & DROPDOWN FILTERS ROW -->
        <div class="p-5 sm:p-6 pt-4 pb-4">
            <form method="GET" action="{{ auth()->check() && !auth()->user()->isAdmin() ? route('timdis.participants') : route('participants.index') }}" id="filter-form" class="grid grid-cols-1 md:grid-cols-[2fr_1fr_1fr_1fr] gap-3">
                
                <!-- Search Input -->
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama atau NPM mahasiswa..."
                        onchange="document.getElementById('filter-form').submit()"
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary placeholder:text-slate-400 transition-colors"
                    >
                </div>

                <!-- Program Studi Dropdown -->
                <div class="relative">
                    <select 
                        name="study_program" 
                        onchange="document.getElementById('filter-form').submit()"
                        class="w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary hover:bg-slate-50 transition-colors font-medium truncate"
                    >
                        <option value="">Semua program studi</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp }}" {{ request('study_program') === $sp ? 'selected' : '' }}>
                                {{ $sp }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-3.5 pointer-events-none"></i>
                </div>

                <!-- Angkatan Dropdown -->
                <div class="relative">
                    <select 
                        name="cohort_year" 
                        onchange="document.getElementById('filter-form').submit()"
                        class="w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary hover:bg-slate-50 transition-colors font-medium truncate"
                    >
                        <option value="">Semua angkatan</option>
                        @foreach($cohortYears as $cy)
                            <option value="{{ $cy }}" {{ request('cohort_year') == $cy ? 'selected' : '' }}>
                                {{ $cy }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-3.5 pointer-events-none"></i>
                </div>

                <!-- Status Dropdown -->
                <div class="relative">
                    <select 
                        name="status" 
                        onchange="document.getElementById('filter-form').submit()"
                        class="w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary hover:bg-slate-50 transition-colors font-medium truncate"
                    >
                        <option value="">Semua status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-3.5 pointer-events-none"></i>
                </div>

                @if(isset($classes) && count($classes) > 1)
                    <!-- Kelas Dropdown -->
                    <div class="relative">
                        <select 
                            name="class_group" 
                            onchange="document.getElementById('filter-form').submit()"
                            class="w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-xl px-3.5 py-2.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary hover:bg-slate-50 transition-colors font-medium truncate"
                        >
                            <option value="">Semua kelas</option>
                            @foreach($classes as $cg)
                                <option value="{{ $cg }}" {{ request('class_group') === $cg ? 'selected' : '' }}>
                                    {{ $cg }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-3.5 pointer-events-none"></i>
                    </div>
                @endif

                @if(request()->hasAny(['search', 'study_program', 'cohort_year', 'class_group', 'status']))
                    <div class="md:col-span-4 flex justify-end">
                        <a href="{{ auth()->check() && !auth()->user()->isAdmin() ? route('timdis.participants') : route('participants.index') }}" class="text-[11.5px] text-[#2D56FF] hover:underline font-semibold flex items-center gap-1">
                            <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                            <span>Reset filter</span>
                        </a>
                    </div>
                @endif

            </form>
        </div>

        <!-- TABLE SECTION -->
        <div class="overflow-x-auto border-t border-slate-100">
            @if($participants->isEmpty())
                <div class="p-12 text-center text-slate-400 text-xs">
                    <p class="font-bold text-slate-700 text-sm">Tidak Ada Data Mahasiswa</p>
                    <p class="text-xs text-slate-400 mt-1">Tidak ditemukan data mahasiswa yang sesuai dengan filter pencarian Anda.</p>
                </div>
            @else
                <table class="w-full text-left text-[12.5px]">
                    <thead class="bg-white border-b border-slate-200/80 text-[11px] font-semibold text-slate-500">
                        <tr>
                            <th class="py-3 px-4 w-10 text-center">
                                <input type="checkbox" id="select-all" class="rounded border-slate-300 text-primary focus:ring-primary/20 cursor-pointer">
                            </th>
                            <th class="py-3 px-4">Mahasiswa</th>
                            <th class="py-3 px-4">NPM</th>
                            <th class="py-3 px-4">Program studi</th>
                            <th class="py-3 px-4">Angkatan</th>
                            <th class="py-3 px-4">Semester</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($participants as $p)
                            @php
                                // Perhitungan estimasi semester berdasarkan angkatan
                                $currentYear = (int) now()->format('Y');
                                $currentMonth = (int) now()->format('m');
                                $cohort = (int) ($p->cohort_year ?? $currentYear);
                                $diffYears = max(0, $currentYear - $cohort);
                                $sem = ($diffYears * 2) + ($currentMonth >= 8 ? 1 : 2);
                                $sem = max(1, min(8, $sem));
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Checkbox -->
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" class="participant-checkbox rounded border-slate-300 text-primary focus:ring-primary/20 cursor-pointer">
                                </td>

                                <!-- Mahasiswa (Nama & Email/Info) -->
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 leading-tight">
                                        {{ $p->name }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ strtolower(str_replace(' ', '.', $p->name)) }}@example.com
                                    </div>
                                </td>

                                <!-- NPM -->
                                <td class="py-3 px-4 font-mono font-medium text-slate-700">
                                    {{ $p->identifier }}
                                </td>

                                <!-- Program Studi & Jurusan -->
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800 leading-tight">
                                        {{ $p->study_program ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $p->department ?? 'Ekonomi dan Bisnis' }}
                                    </div>
                                </td>

                                <!-- Angkatan -->
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $p->cohort_year ?? '-' }}
                                </td>

                                <!-- Semester -->
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $sem }}
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3 px-4">
                                    @if($p->isActive())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E6F8EF] text-[#22C55E]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi Buttons (Hanya Lihat Detail) -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center">
                                        <!-- Lihat / Detail -->
                                        <a 
                                            href="{{ route('participants.show', $p) }}" 
                                            class="w-8 h-8 rounded-lg border border-slate-200 text-slate-500 hover:text-primary hover:border-primary/40 hover:bg-blue-50 flex items-center justify-center transition-colors shadow-2xs" 
                                            title="Lihat Detail Mahasiswa"
                                        >
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- FOOTER: Menampilkan 1-10 dari X data, per halaman select, dan pagination -->
        <div class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white">
            
            <!-- Left: Info & Per Page Dropdown -->
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span>
                    Menampilkan {{ $participants->firstItem() ?? 0 }}–{{ $participants->lastItem() ?? 0 }} dari {{ number_format($participants->total()) }} data
                </span>

                <div class="relative inline-block">
                    <form method="GET" action="{{ auth()->check() && !auth()->user()->isAdmin() ? route('timdis.participants') : route('participants.index') }}" id="per-page-form">
                        @foreach(request()->except(['per_page', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select 
                            name="per_page" 
                            onchange="document.getElementById('per-page-form').submit()"
                            class="bg-white border border-slate-200 text-slate-700 text-xs rounded-lg px-2.5 py-1.5 pr-7 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary font-medium"
                        >
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / halaman</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / halaman</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / halaman</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / halaman</option>
                        </select>
                    </form>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 absolute right-2.5 top-2.5 pointer-events-none"></i>
                </div>
            </div>

            <!-- Right: Custom Styled Pagination Links -->
            <div>
                {{ $participants->links('pagination::tailwind') }}
            </div>

        </div>

    </div>

</div>

<!-- Select All Checkbox Script -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.participant-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        }
    });
</script>
@endpush
@endsection
