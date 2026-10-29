@extends('layouts.app')

@php
    $breadcrumbCategory = 'Menu Utama';
    $breadcrumbPage = 'Data Mahasiswa';

    // 10 Baris Mock Data persis seperti pada desain UI
    $mockStudents = [
        [
            'id' => 1,
            'name' => 'Aditya Pratama',
            'email' => 'aditya.p@example.com',
            'npm' => '23753001',
            'study_program' => 'D3 Manajemen Informatika',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2023',
            'semester' => '5',
            'status' => 'Aktif',
        ],
        [
            'id' => 2,
            'name' => 'Aulia Putri Ramadhani',
            'email' => 'aulia.r@example.com',
            'npm' => '23753008',
            'study_program' => 'D3 Manajemen Informatika',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2023',
            'semester' => '5',
            'status' => 'Aktif',
        ],
        [
            'id' => 3,
            'name' => 'Bima Arya Saputra',
            'email' => 'bima.a@example.com',
            'npm' => '24753012',
            'study_program' => 'D3 Manajemen Informatika',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2024',
            'semester' => '3',
            'status' => 'Aktif',
        ],
        [
            'id' => 4,
            'name' => 'Citra Ayu Lestari',
            'email' => 'citra.a@example.com',
            'npm' => '24752005',
            'study_program' => 'D3 Akuntansi',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2024',
            'semester' => '3',
            'status' => 'Aktif',
        ],
        [
            'id' => 5,
            'name' => 'Daffa Rizky Maulana',
            'email' => 'daffa.r@example.com',
            'npm' => '23752019',
            'study_program' => 'D3 Akuntansi',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2023',
            'semester' => '5',
            'status' => 'Cuti',
        ],
        [
            'id' => 6,
            'name' => 'Dewi Safitri',
            'email' => 'dewi.s@example.com',
            'npm' => '25753006',
            'study_program' => 'D3 Manajemen Informatika',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2025',
            'semester' => '1',
            'status' => 'Aktif',
        ],
        [
            'id' => 7,
            'name' => 'Fajar Nur Hidayat',
            'email' => 'fajar.n@example.com',
            'npm' => '24751011',
            'study_program' => 'D3 Agribisnis',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2024',
            'semester' => '3',
            'status' => 'Aktif',
        ],
        [
            'id' => 8,
            'name' => 'Intan Permata Sari',
            'email' => 'intan.p@example.com',
            'npm' => '23751024',
            'study_program' => 'D3 Agribisnis',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2023',
            'semester' => '5',
            'status' => 'Nonaktif',
        ],
        [
            'id' => 9,
            'name' => 'Muhammad Farhan',
            'email' => 'farhan.m@example.com',
            'npm' => '25752009',
            'study_program' => 'D3 Akuntansi',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2025',
            'semester' => '1',
            'status' => 'Aktif',
        ],
        [
            'id' => 10,
            'name' => 'Nadia Khairunnisa',
            'email' => 'nadia.k@example.com',
            'npm' => '24753027',
            'study_program' => 'D3 Manajemen Informatika',
            'faculty' => 'Ekonomi dan Bisnis',
            'angkatan' => '2024',
            'semester' => '3',
            'status' => 'Aktif',
        ],
    ];
@endphp

@section('content')
<div class="space-y-5 max-w-[1240px] mx-auto pb-8">

    <!-- 2. JUDUL HALAMAN & SUB-JUDUL -->
    <div class="pt-1 pb-1">
        <h1 class="text-[26px] font-bold text-slate-900 tracking-tight leading-tight">
            Data Mahasiswa
        </h1>
        <p class="text-[13.5px] text-slate-500 mt-0.5">
            Kelola identitas mahasiswa dan informasi akademik di SIApel.
        </p>
    </div>

    <!-- 3. CARD UTAMA (KONTAINER TABEL & FILTER) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-5">
        
        <!-- Bagian Atas Card (Header) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Kiri: Ikon Toga, Judul & Badge, Sub-teks -->
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-[16px] font-bold text-slate-900 tracking-tight">
                            Daftar mahasiswa
                        </h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[12px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                            1.248 mahasiswa
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-400 font-normal mt-0.5">
                        Lihat, tambah, dan perbarui data mahasiswa yang terdaftar.
                    </p>
                </div>
            </div>

            <!-- Kanan: Tombol Ekspor data -->
            <div class="flex items-center gap-2 self-start sm:self-center">
                <button 
                    type="button" 
                    onclick="handleExportData()"
                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200/90 text-slate-700 px-3.5 py-2 rounded-xl text-[13px] font-medium shadow-2xs transition-colors cursor-pointer"
                    title="Ekspor Data Mahasiswa"
                >
                    <i data-lucide="download" class="w-4 h-4 text-slate-500"></i>
                    <span>Ekspor data</span>
                </button>
            </div>
        </div>

        <!-- Baris Pencarian & Filter (Grid/Flex) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
            
            <!-- Input Pencarian (Lebar & Dominan) -->
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    id="searchStudentInput" 
                    oninput="filterStudentTable()"
                    placeholder="Cari nama atau NPM mahasiswa..." 
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs"
                >
            </div>

            <!-- Dropdown 1: Semua program studi -->
            <div class="relative min-w-[190px]">
                <select 
                    id="filterStudyProgram" 
                    onchange="filterStudentTable()"
                    class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
                >
                    <option value="">Semua program studi</option>
                    <option value="D3 Manajemen Informatika">D3 Manajemen Informatika</option>
                    <option value="D3 Akuntansi">D3 Akuntansi</option>
                    <option value="D3 Agribisnis">D3 Agribisnis</option>
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            <!-- Dropdown 2: Semua angkatan -->
            <div class="relative min-w-[150px]">
                <select 
                    id="filterAngkatan" 
                    onchange="filterStudentTable()"
                    class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
                >
                    <option value="">Semua angkatan</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            <!-- Dropdown 3: Semua status -->
            <div class="relative min-w-[135px]">
                <select 
                    id="filterStatus" 
                    onchange="filterStudentTable()"
                    class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
                >
                    <option value="">Semua status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

        </div>

        <!-- 4. TABEL DATA MAHASISWA -->
        <div class="border border-slate-100 rounded-xl overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[840px]">
                
                <!-- Header Tabel -->
                <thead class="bg-white border-b border-slate-100 text-[12px] font-medium text-slate-400 select-none">
                    <tr>
                        <th class="w-11 py-3 px-3 text-center">
                            <input 
                                type="checkbox" 
                                id="selectAllCheckbox" 
                                onchange="toggleSelectAll(this)"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer transition-colors"
                            >
                        </th>
                        <th class="py-3 px-4 font-medium text-slate-400">Mahasiswa</th>
                        <th class="py-3 px-4 font-medium text-slate-400">NPM</th>
                        <th class="py-3 px-4 font-medium text-slate-400">Program studi</th>
                        <th class="py-3 px-4 font-medium text-slate-400">Angkatan</th>
                        <th class="py-3 px-4 font-medium text-slate-400">Semester</th>
                        <th class="py-3 px-4 font-medium text-slate-400">Status</th>
                        <th class="py-3 px-4 font-medium text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>

                <!-- Body Tabel (10 Baris Mock Data) -->
                <tbody id="studentTableBody" class="divide-y divide-slate-100/90 text-[13px]">
                    @foreach($mockStudents as $student)
                        <tr 
                            class="student-row hover:bg-slate-50/70 transition-colors group"
                            data-name="{{ strtolower($student['name']) }}"
                            data-npm="{{ $student['npm'] }}"
                            data-program="{{ $student['study_program'] }}"
                            data-angkatan="{{ $student['angkatan'] }}"
                            data-status="{{ $student['status'] }}"
                        >
                            <!-- Kolom Checkbox -->
                            <td class="py-3.5 px-3 text-center">
                                <input 
                                    type="checkbox" 
                                    class="student-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer transition-colors"
                                >
                            </td>

                            <!-- Kolom Mahasiswa: Nama & Email -->
                            <td class="py-3.5 px-4 min-w-[200px]">
                                <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                                    {{ $student['name'] }}
                                </div>
                                <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                                    {{ $student['email'] }}
                                </div>
                            </td>

                            <!-- Kolom NPM -->
                            <td class="py-3.5 px-4 text-[13px] text-slate-700 font-medium">
                                {{ $student['npm'] }}
                            </td>

                            <!-- Kolom Program Studi: Nama Prodi & Fakultas -->
                            <td class="py-3.5 px-4 min-w-[210px]">
                                <div class="font-bold text-[13.5px] text-slate-900 leading-snug">
                                    {{ $student['study_program'] }}
                                </div>
                                <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                                    {{ $student['faculty'] }}
                                </div>
                            </td>

                            <!-- Kolom Angkatan -->
                            <td class="py-3.5 px-4 text-[13px] text-slate-700 font-medium">
                                {{ $student['angkatan'] }}
                            </td>

                            <!-- Kolom Semester -->
                            <td class="py-3.5 px-4 text-[13px] text-slate-700 font-medium">
                                {{ $student['semester'] }}
                            </td>

                            <!-- Kolom Status: Pill dengan Dot Sesuai Varian -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($student['status'] === 'Aktif')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                                        <span>Aktif</span>
                                    </span>
                                @elseif($student['status'] === 'Cuti')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-amber-50 text-amber-600 border border-amber-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block shrink-0"></span>
                                        <span>Cuti</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block shrink-0"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom Aksi: Tombol Kotak Ber-border dengan Ikon Mata -->
                            <td class="py-3.5 px-4 text-center">
                                <button 
                                    type="button" 
                                    onclick="openStudentDetail({{ json_encode($student) }})"
                                    class="w-8 h-8 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors shadow-2xs cursor-pointer mx-auto"
                                    title="Lihat detail {{ $student['name'] }}"
                                >
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- State Kosong Jika Filter Tidak Menemukan Hasil -->
            <div id="noResultsState" class="hidden p-10 text-center text-slate-400 text-[13px]">
                <i data-lucide="search-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                <p class="font-bold text-slate-700 text-[14px]">Data tidak ditemukan</p>
                <p class="text-[12px] text-slate-400 mt-0.5">Tidak ada mahasiswa yang sesuai dengan filter pencarian.</p>
            </div>
        </div>

        <!-- 5. FOOTER CARD: INFO JUMLAH, DROPDOWN PER HALAMAN & PAGINATION -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            
            <!-- Kiri: Menampilkan Data & Dropdown Halaman -->
            <div class="flex items-center gap-3 text-[13px] text-slate-500">
                <span class="font-normal">
                    Menampilkan <span id="visibleRangeText">1–10</span> dari 1.248 data
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

                <!-- Ellipsis -->
                <span class="w-8 h-8 text-slate-400 font-medium text-[13px] flex items-center justify-center">
                    ..
                </span>

                <!-- Page 125 -->
                <button 
                    type="button" 
                    class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-[13px] flex items-center justify-center transition-colors cursor-pointer"
                >
                    125
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

    </div>

    <!-- DISCLAIMER / CATATAN INFORMASI DI BAWAH CARD -->
    <div class="flex items-center gap-2 text-[12.5px] text-slate-400 px-1 pt-1">
        <i data-lucide="info" class="w-4 h-4 text-slate-400 shrink-0"></i>
        <span>Data pada halaman ini adalah contoh, bukan data institusi terverifikasi.</span>
    </div>

</div>

<!-- INTERACTIVE STUDENT DETAIL MODAL -->
<div id="studentDetailModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Top Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <h3 class="text-[16px] font-bold text-slate-900 leading-tight">Detail Mahasiswa</h3>
            <button type="button" onclick="closeStudentModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 space-y-5 text-[13px]">
            
            <!-- Identitas Mahasiswa -->
            <div class="flex items-center gap-3.5 pt-1">
                <div id="modalStudentAvatar" class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 font-bold text-[15px] flex items-center justify-center shrink-0 shadow-xs">
                    AP
                </div>
                <div>
                    <h4 id="modalStudentName" class="text-[16px] font-bold text-slate-900 leading-snug">
                        Aditya Pratama
                    </h4>
                    <p id="modalStudentEmail" class="text-[12.5px] text-slate-400 font-normal">
                        aditya.p@example.com
                    </p>
                </div>
            </div>

            <!-- Detail Grid Info Akademik -->
            <div class="border border-slate-200/90 rounded-xl p-4 bg-slate-50/50 space-y-3.5 text-[13px]">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[11.5px] text-slate-400 font-medium">Nomor Pokok Mahasiswa (NPM)</span>
                        <span id="modalStudentNPM" class="block text-[14px] font-bold text-slate-900 mt-0.5">23753001</span>
                    </div>
                    <div>
                        <span class="block text-[11.5px] text-slate-400 font-medium">Status Keaktifan</span>
                        <div id="modalStudentStatusContainer" class="mt-0.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span id="modalStudentStatusText">Aktif</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200/70 pt-3">
                    <span class="block text-[11.5px] text-slate-400 font-medium">Program Studi & Fakultas</span>
                    <span id="modalStudentProdi" class="block text-[13.5px] font-bold text-slate-900 mt-0.5">D3 Manajemen Informatika</span>
                    <span id="modalStudentFaculty" class="block text-[12px] text-slate-500 font-normal mt-0.2">Ekonomi dan Bisnis</span>
                </div>

                <div class="border-t border-slate-200/70 pt-3 grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[11.5px] text-slate-400 font-medium">Angkatan</span>
                        <span id="modalStudentAngkatan" class="block text-[13.5px] font-semibold text-slate-900 mt-0.5">2023</span>
                    </div>
                    <div>
                        <span class="block text-[11.5px] text-slate-400 font-medium">Semester Berjalan</span>
                        <span id="modalStudentSemester" class="block text-[13.5px] font-semibold text-slate-900 mt-0.5">Semester 5</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Tutup -->
            <div class="pt-1 flex justify-end">
                <button 
                    type="button" 
                    onclick="closeStudentModal()"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[13px] rounded-xl transition-colors cursor-pointer"
                >
                    Tutup
                </button>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
    // 1. Checkbox Select All Toggle
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
    }

    // Update master checkbox jika ada child checkbox yang diklik manual
    document.addEventListener('DOMContentLoaded', function() {
        const childCheckboxes = document.querySelectorAll('.student-checkbox');
        const master = document.getElementById('selectAllCheckbox');
        
        childCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const total = childCheckboxes.length;
                const checked = document.querySelectorAll('.student-checkbox:checked').length;
                if (master) {
                    master.checked = total > 0 && total === checked;
                    master.indeterminate = checked > 0 && checked < total;
                }
            });
        });
    });

    // 2. Real-time Client-Side Filtering
    function filterStudentTable() {
        const searchVal = document.getElementById('searchStudentInput').value.toLowerCase().trim();
        const prodiVal = document.getElementById('filterStudyProgram').value;
        const angkatanVal = document.getElementById('filterAngkatan').value;
        const statusVal = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('.student-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const npm = row.getAttribute('data-npm') || '';
            const program = row.getAttribute('data-program') || '';
            const angkatan = row.getAttribute('data-angkatan') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = !searchVal || name.includes(searchVal) || npm.includes(searchVal);
            const matchesProdi = !prodiVal || program === prodiVal;
            const matchesAngkatan = !angkatanVal || angkatan === angkatanVal;
            const matchesStatus = !statusVal || status === statusVal;

            if (matchesSearch && matchesProdi && matchesAngkatan && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResultsState');
        const rangeText = document.getElementById('visibleRangeText');

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            if (rangeText) rangeText.textContent = '0–0';
        } else {
            noResults.classList.add('hidden');
            if (rangeText) rangeText.textContent = `1–${visibleCount}`;
        }
    }

    // 3. Student Detail Modal View
    function openStudentDetail(student) {
        if (!student) return;

        const name = student.name || 'Mahasiswa';
        const nameParts = name.trim().split(' ');
        let initials = (nameParts[0] ? nameParts[0][0] : 'M') + (nameParts[1] ? nameParts[1][0] : '');
        initials = initials.toUpperCase();

        document.getElementById('modalStudentAvatar').textContent = initials;
        document.getElementById('modalStudentName').textContent = name;
        document.getElementById('modalStudentEmail').textContent = student.email || '-';
        document.getElementById('modalStudentNPM').textContent = student.npm || '-';
        document.getElementById('modalStudentProdi').textContent = student.study_program || '-';
        document.getElementById('modalStudentFaculty').textContent = student.faculty || '-';
        document.getElementById('modalStudentAngkatan').textContent = student.angkatan || '-';
        document.getElementById('modalStudentSemester').textContent = 'Semester ' + (student.semester || '1');

        const statusContainer = document.getElementById('modalStudentStatusContainer');
        const status = student.status || 'Aktif';

        if (status === 'Aktif') {
            statusContainer.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                    <span>Aktif</span>
                </span>`;
        } else if (status === 'Cuti') {
            statusContainer.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-amber-50 text-amber-600 border border-amber-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block shrink-0"></span>
                    <span>Cuti</span>
                </span>`;
        } else {
            statusContainer.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block shrink-0"></span>
                    <span>Nonaktif</span>
                </span>`;
        }

        document.getElementById('studentDetailModal').classList.remove('hidden');
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeStudentModal() {
        document.getElementById('studentDetailModal').classList.add('hidden');
    }

    // 4. Ekspor Data Handler
    function handleExportData() {
        window.location.href = "{{ route('admin.reports.export-csv') }}";
    }
</script>
@endpush
@endsection
