<!-- BAGIAN 2: TABEL DAFTAR JADWAL KHUSUS (CARD LEBAR) -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-4">
    
    <!-- Header Card: Judul, Badge & Urutan -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <h2 class="text-[16px] font-bold text-slate-900 tracking-tight">Daftar jadwal khusus</h2>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[12px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                12 jadwal
            </span>
        </div>

        <div class="flex items-center gap-1.5 text-[12.5px] text-slate-500 font-medium cursor-pointer hover:text-slate-800 transition-colors select-none">
            <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-slate-400"></i>
            <span>Terbaru dibuat</span>
        </div>
    </div>

    <!-- Filter Bar: Search Input & 3 Dropdowns -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
        
        <!-- Input Pencarian (Lebar) -->
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input 
                type="text" 
                id="searchScheduleInput"
                oninput="filterScheduleTable()"
                placeholder="Cari nama kegiatan atau lokasi..." 
                class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs"
            >
        </div>

        <!-- Dropdown 1: Bulan/Tahun dengan Ikon Kalender -->
        <div class="relative min-w-[155px]">
            <i data-lucide="calendar" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <select 
                class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl pl-9 pr-8 py-2 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
            >
                <option value="2026-10">Oktober 2026</option>
                <option value="2026-09">September 2026</option>
                <option value="2026-08">Agustus 2026</option>
            </select>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>

        <!-- Dropdown 2: Semua status -->
        <div class="relative min-w-[140px]">
            <select 
                id="filterScheduleStatus"
                onchange="filterScheduleTable()"
                class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
            >
                <option value="">Semua status</option>
                <option value="Akan datang">Akan datang</option>
                <option value="Selesai">Selesai</option>
                <option value="Draf">Draf</option>
            </select>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>

        <!-- Dropdown 3: Semua jurusan -->
        <div class="relative min-w-[150px]">
            <select 
                id="filterScheduleJurusan"
                onchange="filterScheduleTable()"
                class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs"
            >
                <option value="">Semua jurusan</option>
                <option value="Teknologi Informasi">Teknologi Informasi</option>
                <option value="Produksi Tanaman Pangan">Produksi Tanaman Pangan</option>
                <option value="Teknologi Pertanian">Teknologi Pertanian</option>
                <option value="Ekonomi dan Bisnis">Ekonomi dan Bisnis</option>
            </select>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>

    </div>

    <!-- Tabel Data Jadwal Khusus -->
    <div class="border border-slate-100 rounded-xl overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[760px]">
            
            <!-- Header Kolom -->
            <thead class="bg-white border-b border-slate-100 text-[12px] font-medium text-slate-400 select-none">
                <tr>
                    <th class="py-3 px-4 font-medium text-slate-400">Nama kegiatan</th>
                    <th class="py-3 px-4 font-medium text-slate-400">Tanggal & waktu</th>
                    <th class="py-3 px-4 font-medium text-slate-400">Cakupan peserta</th>
                    <th class="py-3 px-4 font-medium text-slate-400">Lokasi</th>
                </tr>
            </thead>

            <!-- Tabel Data: Dinamis jika ada data DB, Fallback Statis jika kosong -->
            <tbody id="scheduleTableBody" class="divide-y divide-slate-100/90 text-[13px]">
                @if(isset($schedules) && $schedules->count() > 0)
                    @foreach($schedules as $sched)
                        <tr class="schedule-row hover:bg-slate-50/70 transition-colors group cursor-pointer" data-title="{{ strtolower($sched->title) }}" data-location="{{ strtolower($sched->location) }}" data-scope="seluruh mahasiswa">
                            <td class="py-3.5 px-4 min-w-[220px]">
                                <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                                    {{ $sched->title }}
                                </div>
                                <div class="text-[12px] text-slate-400 font-normal mt-0.5 capitalize">
                                    Kegiatan {{ $sched->category }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 min-w-[180px]">
                                <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                                    {{ $sched->schedule_date ? \Carbon\Carbon::parse($sched->schedule_date)->translatedFormat('l, d M Y') : '-' }}
                                </div>
                                <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                                    {{ substr($sched->start_time, 0, 5) }}–{{ substr($sched->end_time, 0, 5) }} WIB
                                </div>
                            </td>
                            <td class="py-3.5 px-4 min-w-[190px]">
                                <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                                    Peserta Apel
                                </div>
                                <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                                    {{ $sched->attendance_records_count ?? 0 }} mahasiswa
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-[13px] text-slate-700">
                                {{ $sched->location }}
                            </td>
                        </tr>
                    @endforeach
                @else
                <!-- Baris 1: Upacara Hari Sumpah Pemuda -->
                <tr class="schedule-row hover:bg-slate-50/70 transition-colors group cursor-pointer" data-title="upacara hari sumpah pemuda" data-location="lapangan utama" data-scope="seluruh mahasiswa">
                    <td class="py-3.5 px-4 min-w-[220px]">
                        <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                            Upacara Hari Sumpah Pemuda
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            Kegiatan kampus
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[180px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Rabu, 28 Okt 2026
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            07.00–08.30 WIB
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[190px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Seluruh mahasiswa
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            3.329 mahasiswa
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-medium text-[13px] text-slate-700">
                        Lapangan utama
                    </td>
                </tr>

                <!-- Baris 2: Pembinaan kedisiplinan -->
                <tr class="schedule-row hover:bg-slate-50/70 transition-colors group cursor-pointer" data-title="pembinaan kedisiplinan" data-location="gedung sakura" data-scope="teknologi informasi">
                    <td class="py-3.5 px-4 min-w-[220px]">
                        <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                            Pembinaan kedisiplinan
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            Pembinaan mahasiswa
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[180px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Senin, 26 Okt 2026
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            15.30–17.00 WIB
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[190px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Teknologi Informasi
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            248 mahasiswa • Angkatan 2025
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-medium text-[13px] text-slate-700">
                        Gedung Sakura
                    </td>
                </tr>

                <!-- Baris 3: Pengarahan praktik lapangan -->
                <tr class="schedule-row hover:bg-slate-50/70 transition-colors group cursor-pointer" data-title="pengarahan praktik lapangan" data-location="aula pertanian" data-scope="produksi tanaman pangan">
                    <td class="py-3.5 px-4 min-w-[220px]">
                        <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                            Pengarahan praktik lapangan
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            Kegiatan program studi
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[180px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Jumat, 30 Okt 2026
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            08.00–09.00 WIB
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[190px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Produksi Tanaman Pangan
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            96 mahasiswa • Angkatan 2024
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-medium text-[13px] text-slate-700">
                        Aula pertanian
                    </td>
                </tr>

                <!-- Baris 4: Apel pembinaan mahasiswa -->
                <tr class="schedule-row hover:bg-slate-50/70 transition-colors group cursor-pointer" data-title="apel pembinaan mahasiswa" data-location="lapangan utama" data-scope="seluruh mahasiswa">
                    <td class="py-3.5 px-4 min-w-[220px]">
                        <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                            Apel pembinaan mahasiswa
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            Kegiatan kampus
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[180px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Rabu, 21 Okt 2026
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            07.00–07.30 WIB
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[190px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Seluruh mahasiswa
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            3.329 mahasiswa
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-medium text-[13px] text-slate-700">
                        Lapangan utama
                    </td>
                </tr>

                <!-- Baris 5: Sosialisasi keselamatan kerja -->
                <tr class="schedule-row hover:bg-slate-50/70 transition-colors group cursor-pointer" data-title="sosialisasi keselamatan kerja" data-location="auditorium polinela" data-scope="teknologi pertanian">
                    <td class="py-3.5 px-4 min-w-[220px]">
                        <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                            Sosialisasi keselamatan kerja
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            Kegiatan jurusan
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[180px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Senin, 19 Okt 2026
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            09.00–11.00 WIB
                        </div>
                    </td>
                    <td class="py-3.5 px-4 min-w-[190px]">
                        <div class="font-semibold text-[13px] text-slate-800 leading-snug">
                            Teknologi Pertanian
                        </div>
                        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
                            180 mahasiswa • Angkatan 2025
                        </div>
                    </td>
                    <td class="py-3.5 px-4 font-medium text-[13px] text-slate-700">
                        Auditorium Polinela
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Empty state fallback -->
        <div id="noScheduleResults" class="hidden p-8 text-center text-slate-400 text-[13px]">
            <i data-lucide="search-x" class="w-8 h-8 mx-auto text-slate-300 mb-1.5"></i>
            <p class="font-bold text-slate-700">Jadwal tidak ditemukan</p>
            <p class="text-[12px] text-slate-400 mt-0.5">Tidak ada jadwal yang cocok dengan kata kunci filter Anda.</p>
        </div>
    </div>

    <!-- Footer Card: Info Jumlah & Pagination -->
    <div class="flex items-center justify-between pt-1 select-none">
        <span class="text-[13px] text-slate-400 font-normal">
            Menampilkan <span id="scheduleShowingCount">1–5</span> dari 12 jadwal
        </span>

        <div class="flex items-center gap-1">
            <!-- Prev Button -->
            <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-600 cursor-pointer transition-colors">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>

            <!-- Page 1 Active -->
            <span class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold text-[13px] flex items-center justify-center shadow-xs">
                1
            </span>

            <!-- Page 2 -->
            <button type="button" class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-[13px] flex items-center justify-center transition-colors cursor-pointer">
                2
            </button>

            <!-- Page 3 -->
            <button type="button" class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-[13px] flex items-center justify-center transition-colors cursor-pointer">
                3
            </button>

            <!-- Next Button -->
            <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 cursor-pointer transition-colors">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

</div>
