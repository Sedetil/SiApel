<!-- AREA FORM (KIRI - STEP 1, 2, 3) -->
<div class="space-y-8">
    
    <!-- STEP 1: INFORMASI KEGIATAN -->
    <div class="space-y-4">
        <!-- Step Header -->
        <div class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold text-[12px] flex items-center justify-center shrink-0">
                1
            </span>
            <h3 class="text-[15px] font-bold text-slate-900 tracking-tight">
                Informasi kegiatan
            </h3>
        </div>

        <!-- Form Fields Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pl-9">
            <!-- Nama kegiatan -->
            <div class="space-y-1.5">
                <label for="inputEventName" class="block text-[12px] font-semibold text-slate-700">
                    Nama kegiatan <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="inputEventName"
                    value="Upacara Kemerdekaan Indonesia"
                    placeholder="Contoh: Upacara Kemerdekaan RI"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                >
            </div>

            <!-- Jenis kegiatan -->
            <div class="space-y-1.5">
                <label for="selectEventType" class="block text-[12px] font-semibold text-slate-700">
                    Jenis kegiatan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select 
                        id="selectEventType"
                        class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2.5 pr-8 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs font-normal"
                    >
                        <option value="Kegiatan kampus" selected>Kegiatan kampus</option>
                        <option value="Pembinaan mahasiswa">Pembinaan mahasiswa</option>
                        <option value="Kegiatan program studi">Kegiatan program studi</option>
                        <option value="Kegiatan jurusan">Kegiatan jurusan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 2: CAKUPAN PESERTA -->
    <div class="space-y-4">
        <!-- Step Header -->
        <div class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold text-[12px] flex items-center justify-center shrink-0">
                2
            </span>
            <h3 class="text-[15px] font-bold text-slate-900 tracking-tight">
                Cakupan peserta
            </h3>
        </div>

        <div class="space-y-3.5 pl-9">
            <!-- 3 Pilihan Radio Button Card -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                
                <!-- Option 1: Seluruh mahasiswa (Aktif) -->
                <label class="relative flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-blue-600 bg-white cursor-pointer transition-all shadow-2xs select-none group">
                    <input type="radio" name="cakupan_peserta" value="all" checked class="sr-only peer">
                    <div class="w-4 h-4 rounded-full border border-blue-600 flex items-center justify-center shrink-0">
                        <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span class="text-[12.5px] font-semibold text-blue-600">
                        Seluruh mahasiswa
                    </span>
                </label>

                <!-- Option 2: Jurusan / program studi -->
                <label class="relative flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50/70 cursor-pointer transition-all shadow-2xs select-none group">
                    <input type="radio" name="cakupan_peserta" value="jurusan" class="sr-only peer">
                    <div class="w-4 h-4 rounded-full border border-slate-300 group-hover:border-slate-400 flex items-center justify-center shrink-0">
                        <div class="w-2 h-2 rounded-full bg-transparent"></div>
                    </div>
                    <span class="text-[12.5px] font-medium text-slate-700 group-hover:text-slate-900">
                        Jurusan / program studi
                    </span>
                </label>

                <!-- Option 3: Mahasiswa tertentu -->
                <label class="relative flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50/70 cursor-pointer transition-all shadow-2xs select-none group">
                    <input type="radio" name="cakupan_peserta" value="spesifik" class="sr-only peer">
                    <div class="w-4 h-4 rounded-full border border-slate-300 group-hover:border-slate-400 flex items-center justify-center shrink-0">
                        <div class="w-2 h-2 rounded-full bg-transparent"></div>
                    </div>
                    <span class="text-[12.5px] font-medium text-slate-700 group-hover:text-slate-900">
                        Mahasiswa tertentu
                    </span>
                </label>

            </div>

            <!-- Dropdown Angkatan & Indikator Jumlah Tercakup -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-1">
                <div class="w-full sm:w-1/2 space-y-1.5">
                    <label for="selectCohort" class="block text-[12px] font-semibold text-slate-700">
                        Angkatan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="selectCohort"
                            class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2.5 pr-8 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs font-normal"
                        >
                            <option value="all" selected>Semua angkatan</option>
                            <option value="2026">Angkatan 2026</option>
                            <option value="2025">Angkatan 2025</option>
                            <option value="2024">Angkatan 2024</option>
                            <option value="2023">Angkatan 2023</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Indikator Biru Mahasiswa Tercakup -->
                <div class="flex items-center gap-2 text-blue-600 font-medium text-[12.5px] sm:self-end sm:pb-3 select-none">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                    <span>3.329 mahasiswa aktif tercakup</span>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3: TANGGAL, WAKTU & LOKASI -->
    <div class="space-y-4">
        <!-- Step Header -->
        <div class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold text-[12px] flex items-center justify-center shrink-0">
                3
            </span>
            <h3 class="text-[15px] font-bold text-slate-900 tracking-tight">
                Tanggal, waktu & lokasi
            </h3>
        </div>

        <div class="space-y-4 pl-9">
            
            <!-- Baris 1: Tanggal mulai & Tanggal selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tanggal mulai -->
                <div class="space-y-1.5">
                    <label for="inputStartDate" class="block text-[12px] font-semibold text-slate-700">
                        Tanggal mulai <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="inputStartDate"
                            value="17 Agustus 2026"
                            class="w-full px-3.5 py-2.5 pr-10 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                        >
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Tanggal selesai -->
                <div class="space-y-1.5">
                    <label for="inputEndDate" class="block text-[12px] font-semibold text-slate-700">
                        Tanggal selesai <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="inputEndDate"
                            value="17 Agustus 2026"
                            class="w-full px-3.5 py-2.5 pr-10 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                        >
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Baris 2: Jam mulai, Jam selesai, Toleransi terlambat -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Jam mulai -->
                <div class="space-y-1.5">
                    <label for="inputStartTime" class="block text-[12px] font-semibold text-slate-700">
                        Jam mulai <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="inputStartTime"
                            value="07.00 WIB"
                            class="w-full px-3.5 py-2.5 pr-10 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                        >
                        <i data-lucide="clock" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Jam selesai -->
                <div class="space-y-1.5">
                    <label for="inputEndTime" class="block text-[12px] font-semibold text-slate-700">
                        Jam selesai <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="inputEndTime"
                            value="08.30 WIB"
                            class="w-full px-3.5 py-2.5 pr-10 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                        >
                        <i data-lucide="clock" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Toleransi terlambat -->
                <div class="space-y-1.5">
                    <label for="selectLateTolerance" class="block text-[12px] font-semibold text-slate-700">
                        Toleransi terlambat <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="selectLateTolerance"
                            class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2.5 pr-8 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs font-normal"
                        >
                            <option value="5 menit">5 menit</option>
                            <option value="10 menit">10 menit</option>
                            <option value="15 menit" selected>15 menit</option>
                            <option value="30 menit">30 menit</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Baris 3: Lokasi absensi & Penanggung jawab -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Lokasi absensi -->
                <div class="space-y-1.5">
                    <label for="inputLocation" class="block text-[12px] font-semibold text-slate-700">
                        Lokasi absensi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="inputLocation"
                            value="Lapangan GSG"
                            class="w-full px-3.5 py-2.5 pr-10 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                        >
                        <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Penanggung jawab -->
                <div class="space-y-1.5">
                    <label for="selectResponsiblePerson" class="block text-[12px] font-semibold text-slate-700">
                        Penanggung jawab <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="selectResponsiblePerson"
                            class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2.5 pr-8 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs font-normal"
                        >
                            <option value="Agus Saputra · Timdis" selected>Agus Saputra · Timdis</option>
                            <option value="I Komang Artayasa · Timdis">I Komang Artayasa · Timdis</option>
                            <option value="Bambang Supriyanto · Timdis">Bambang Supriyanto · Timdis</option>
                            <option value="Siti Nurhaliza · Pembina">Siti Nurhaliza · Pembina</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Baris 4: Keterangan (textarea) -->
            <div class="space-y-1.5">
                <label for="inputDescription" class="block text-[12px] font-semibold text-slate-700">
                    Keterangan
                </label>
                <textarea 
                    id="inputDescription" 
                    rows="3"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs resize-none font-normal"
                    placeholder="Tambahkan keterangan atau instruksi bagi peserta..."
                >Mahasiswa wajib mengenakan seragam almamater dan hadir 15 menit sebelum upacara dimulai.</textarea>
            </div>

        </div>
    </div>

</div>
