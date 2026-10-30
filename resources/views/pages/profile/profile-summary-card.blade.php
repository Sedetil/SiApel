<!-- CARD RINGKASAN PROFIL (KOLOM KANAN) -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-6">
    
    <!-- Bagian Atas: Avatar, Nama, Role & Status (Center Aligned) -->
    <div class="flex flex-col items-center text-center pt-2">
        
        <!-- Avatar Inisial B (Biru Muda & Teks Biru Tebal) -->
        <div style="width: 76px; height: 76px;" class="w-[76px] h-[76px] rounded-2xl bg-blue-50/80 border border-blue-100 flex items-center justify-center shrink-0 shadow-2xs mx-auto">
            <span class="text-[32px] font-bold text-blue-600 leading-none">
                B
            </span>
        </div>

        <!-- Nama Pengguna -->
        <h3 id="summaryUserName" class="text-[17px] font-bold text-slate-900 tracking-tight mt-4 leading-snug">
            {{ old('name', 'I Komang Artayasa') }}
        </h3>

        <!-- Badge Role -->
        <div class="mt-1.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11.5px] font-semibold bg-blue-50 text-blue-600 border border-blue-100/90">
                Admin
            </span>
        </div>

        <!-- Status Akun Aktif -->
        <div class="flex items-center justify-center gap-1.5 mt-2.5 text-[12px] text-slate-600 font-medium select-none">
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0"></span>
            <span>Akun aktif</span>
        </div>

    </div>

    <!-- Garis Pemisah (Divider) -->
    <div class="border-b border-slate-100"></div>

    <!-- Bagian Bawah: Identitas Pengguna (Left Aligned) -->
    <div class="space-y-4">
        
        <!-- Judul Seksi -->
        <h4 class="text-[14px] font-bold text-slate-900 tracking-tight">
            Identitas Pengguna
        </h4>

        <!-- Field Readonly: Peran akun -->
        <div class="space-y-1.5">
            <label class="block text-[12px] font-semibold text-slate-700">
                Peran akun
            </label>
            <div class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-[13px] text-slate-700 font-medium flex items-center justify-between select-none shadow-2xs">
                <span>Admin</span>
                <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
            </div>
        </div>

        <!-- Alert Box: Data Institusi Dikelola Oleh Admin -->
        <div class="bg-slate-50/90 border border-slate-100 rounded-xl p-3.5 flex items-start gap-2.5">
            <i data-lucide="info" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
            <p class="text-[11.5px] text-slate-500 leading-relaxed font-normal">
                Data institusi dikelola oleh admin. Hubungi admin jika perlu perubahan.
            </p>
        </div>

    </div>

</div>
