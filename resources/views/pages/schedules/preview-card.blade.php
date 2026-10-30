<!-- AREA PRATINJAU (KANAN - 1/3) -->
<div class="bg-slate-50/80 border border-slate-100 rounded-2xl p-5 sm:p-6 space-y-6">
    
    <!-- Header Pratinjau: Judul & Badge "Baru" -->
    <div class="flex items-center justify-between">
        <h4 class="text-[14.5px] font-bold text-slate-900 tracking-tight">
            Pratinjau jadwal
        </h4>
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-700">
            Baru
        </span>
    </div>

    <!-- Box Tanggal & Judul Besar -->
    <div class="flex items-center gap-3.5 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
        <!-- Box Tanggal Agust 17 -->
        <div class="w-14 h-14 rounded-2xl bg-blue-50/90 border border-blue-200/90 flex flex-col items-center justify-center shrink-0 py-1.5 px-2 select-none shadow-2xs">
            <span id="previewBadgeMonth" class="text-[10.5px] font-bold text-blue-600 uppercase tracking-wider leading-none">
                Agust
            </span>
            <span id="previewBadgeDay" class="text-[20px] font-extrabold text-blue-700 leading-none mt-1">
                17
            </span>
        </div>

        <!-- Judul Besar Kegiatan -->
        <div class="min-w-0 flex-1">
            <h3 id="previewTitle" class="text-[16px] font-bold text-slate-900 leading-snug">
                Upacara 17 Agustus 2026
            </h3>
        </div>
    </div>

    <!-- List Detail Pratinjau -->
    <div class="space-y-4 text-[13px]">
        
        <!-- 1. Tanggal kegiatan -->
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200/80 text-slate-400 flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="min-w-0">
                <span class="block text-[11px] text-slate-400 font-medium leading-none mb-1">
                    Tanggal kegiatan
                </span>
                <span id="previewDate" class="block font-semibold text-slate-800 text-[13px] leading-snug">
                    Rabu, 17 Agustus 2026
                </span>
            </div>
        </div>

        <!-- 2. Waktu absensi -->
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200/80 text-slate-400 flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="min-w-0">
                <span class="block text-[11px] text-slate-400 font-medium leading-none mb-1">
                    Waktu absensi
                </span>
                <span id="previewTime" class="block font-semibold text-slate-800 text-[13px] leading-snug">
                    07.00 - 08.30 WIB
                </span>
                <span id="previewTolerance" class="block text-[11.5px] text-slate-400 font-normal mt-0.5">
                    Toleransi keterlambatan 15 menit
                </span>
            </div>
        </div>

        <!-- 3. Peserta -->
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200/80 text-slate-400 flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="users" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="min-w-0">
                <span class="block text-[11px] text-slate-400 font-medium leading-none mb-1">
                    Peserta
                </span>
                <span id="previewParticipantScope" class="block font-semibold text-slate-800 text-[13px] leading-snug">
                    Seluruh mahasiswa
                </span>
                <span id="previewParticipantDetail" class="block text-[11.5px] text-slate-400 font-normal mt-0.5">
                    Semua angkatan · 3.329 mahasiswa
                </span>
            </div>
        </div>

        <!-- 4. Lokasi -->
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200/80 text-slate-400 flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="min-w-0">
                <span class="block text-[11px] text-slate-400 font-medium leading-none mb-1">
                    Lokasi
                </span>
                <span id="previewLocation" class="block font-semibold text-slate-800 text-[13px] leading-snug">
                    Lapangan GSG
                </span>
            </div>
        </div>

        <!-- 5. Penanggung jawab -->
        <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200/80 text-slate-400 flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="min-w-0">
                <span class="block text-[11px] text-slate-400 font-medium leading-none mb-1">
                    Penanggung jawab
                </span>
                <span id="previewPIC" class="block font-semibold text-slate-800 text-[13px] leading-snug">
                    Agus Saputra
                </span>
            </div>
        </div>

    </div>

    <!-- Alert Info (Biru): Periksa sebelum menyimpan -->
    <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-3.5 flex items-start gap-2.5">
        <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
        <div class="min-w-0">
            <h5 class="text-[12.5px] font-bold text-blue-900 leading-snug">
                Periksa sebelum menyimpan
            </h5>
            <p class="text-[11.5px] text-blue-700/80 leading-relaxed mt-1">
                Jadwal yang disimpan akan tampil pada akun mahasiswa sesuai cakupan peserta. Jadwal reguler tetap berlaku di luar waktu kegiatan ini.
            </p>
        </div>
    </div>

</div>
