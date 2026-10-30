<!-- MODAL 1: TAMBAH ANGGOTA TIMDIS -->
<div id="createOfficerModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900 leading-tight">Tambah Anggota Timdis</h3>
            </div>
            <button type="button" onclick="closeCreateOfficerModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form Tambah -->
        <form method="POST" action="{{ route('admin.officers.store') }}" class="p-6 space-y-4 text-[13px]">
            @csrf
            <div>
                <label for="create_name" class="block text-[12px] font-medium text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="create_name" required placeholder="Contoh: Ahmad Fauzi" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="create_username" class="block text-[12px] font-medium text-slate-600 mb-1">Username</label>
                    <input type="text" name="username" id="create_username" required placeholder="timdis.ahmad" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>
                <div>
                    <label for="create_phone" class="block text-[12px] font-medium text-slate-600 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="create_phone" placeholder="0812 0000 0101" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>
            </div>

            <div>
                <label for="create_email" class="block text-[12px] font-medium text-slate-600 mb-1">Email</label>
                <input type="email" name="email" id="create_email" required placeholder="ahmad.f@example.com" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="create_role" class="block text-[12px] font-medium text-slate-600 mb-1">Peran</label>
                    <select name="role" id="create_role" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="Anggota">Anggota</option>
                        <option value="Koordinator">Koordinator</option>
                        <option value="Sekretaris">Sekretaris</option>
                    </select>
                </div>
                <div>
                    <label for="create_assignment" class="block text-[12px] font-medium text-slate-600 mb-1">Penugasan</label>
                    <select name="assignment" id="create_assignment" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="Lapangan GSG">Lapangan GSG</option>
                        <option value="Lapangan GKB">Lapangan GKB</option>
                        <option value="Gerbang Utama">Gerbang Utama</option>
                        <option value="Parkir Terpadu">Parkir Terpadu</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="create_password" class="block text-[12px] font-medium text-slate-600 mb-1">Kata Sandi Default</label>
                <input type="password" name="password" id="create_password" required placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeCreateOfficerModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors cursor-pointer shadow-xs">
                    Simpan Anggota
                </button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL 2: UBAH DATA TIMDIS -->
<div id="editOfficerModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900 leading-tight">Ubah Data Timdis</h3>
            </div>
            <button type="button" onclick="closeEditOfficerModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form Ubah -->
        <div class="p-6 space-y-4 text-[13px]">
            <div>
                <label class="block text-[12px] font-medium text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" id="edit_name" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 mb-1">Username</label>
                    <input type="text" id="edit_username" readonly class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-500 bg-slate-50 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 mb-1">Nomor Telepon</label>
                    <input type="text" id="edit_phone" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-[12px] font-medium text-slate-600 mb-1">Email</label>
                <input type="email" id="edit_email" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 mb-1">Peran</label>
                    <select id="edit_role" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="Anggota">Anggota</option>
                        <option value="Koordinator">Koordinator</option>
                        <option value="Sekretaris">Sekretaris</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 mb-1">Penugasan</label>
                    <select id="edit_assignment" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="Lapangan GSG">Lapangan GSG</option>
                        <option value="Lapangan GKB">Lapangan GKB</option>
                        <option value="Gerbang Utama">Gerbang Utama</option>
                        <option value="Parkir Terpadu">Parkir Terpadu</option>
                    </select>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeEditOfficerModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="saveEditOfficer()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors cursor-pointer shadow-xs">
                    Perbarui Data
                </button>
            </div>
        </div>

    </div>
</div>

<!-- MODAL 3: RESET PASSWORD / AKSES -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900 leading-tight">Reset Password Akun</h3>
            </div>
            <button type="button" onclick="closeResetPasswordModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 space-y-4 text-[13px]">
            <p class="text-slate-600 leading-relaxed">
                Anda akan mereset password untuk akun <strong id="resetTargetName" class="text-slate-900">Ahmad Fauzi</strong> (<span id="resetTargetUsername" class="font-mono text-blue-600">timdis.ahmad</span>).
            </p>

            <div>
                <label class="block text-[12px] font-medium text-slate-600 mb-1">Password Baru Sementara</label>
                <input type="text" id="new_temp_password" value="Polinela@2026" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-slate-800 font-mono text-[13px] bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                <span class="block text-[11px] text-slate-400 mt-1">Berikan password ini kepada anggota untuk login pertama kali.</span>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="confirmResetPassword()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors cursor-pointer shadow-xs">
                    Konfirmasi Reset
                </button>
            </div>
        </div>

    </div>
</div>

<!-- MODAL 4: UBAH STATUS AKUN (AKTIF / NONAKTIF) -->
<div id="toggleStatusModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div id="statusModalIconContainer" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <i data-lucide="user-x" class="w-4 h-4"></i>
                </div>
                <h3 id="statusModalTitle" class="text-[16px] font-bold text-slate-900 leading-tight">Konfirmasi Nonaktifkan Akun</h3>
            </div>
            <button type="button" onclick="closeToggleStatusModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 space-y-4 text-[13px]">
            <p id="statusModalMessage" class="text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menonaktifkan akun anggota timdis ini? Anggota tidak akan dapat masuk ke sistem absensi selama akun berstatus nonaktif.
            </p>

            <div class="pt-2 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeToggleStatusModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" id="statusModalConfirmBtn" onclick="confirmToggleStatus()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors cursor-pointer shadow-xs">
                    Nonaktifkan
                </button>
            </div>
        </div>

    </div>
</div>
