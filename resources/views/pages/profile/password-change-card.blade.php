<!-- CARD 2: UBAH KATA SANDI -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-5">
    
    <!-- Header Card -->
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
            <i data-lucide="shield-check" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-[16px] font-bold text-slate-900 tracking-tight leading-snug">
                Ubah kata sandi
            </h2>
            <p class="text-[12.5px] text-slate-500 mt-0.5 leading-snug">
                Kosongkan bagian ini jika tidak ingin mengubah kata sandi.
            </p>
        </div>
    </div>

    <!-- Divider Line -->
    <div class="border-b border-slate-100"></div>

    <!-- Form Fields -->
    <div class="space-y-4 pt-1">
        
        <!-- Field 1 (Lebar Penuh): Kata sandi saat ini -->
        <div class="space-y-1.5">
            <label for="current_password" class="block text-[12px] font-semibold text-slate-700">
                Kata sandi saat ini
            </label>
            <div class="relative">
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    placeholder="Masukkan kata sandi saat ini"
                    class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                >
                <button 
                    type="button" 
                    onclick="togglePasswordVisibility('current_password', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1 cursor-pointer focus:outline-none"
                    title="Tampilkan / Sembunyikan Kata Sandi"
                >
                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                </button>
            </div>
            @error('current_password')
                <p class="text-[11.5px] text-red-500 font-medium mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field 2 & 3 (Grid 2 Kolom): Kata sandi baru & Konfirmasi kata sandi baru -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Kiri: Kata sandi baru -->
            <div class="space-y-1.5">
                <label for="password" class="block text-[12px] font-semibold text-slate-700">
                    Kata sandi baru
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan kata sandi baru"
                        class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                    >
                    <button 
                        type="button" 
                        onclick="togglePasswordVisibility('password', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1 cursor-pointer focus:outline-none"
                        title="Tampilkan / Sembunyikan Kata Sandi"
                    >
                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-[11.5px] text-red-500 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kanan: Konfirmasi kata sandi baru -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-[12px] font-semibold text-slate-700">
                    Konfirmasi kata sandi baru
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Ulangi kata sandi baru"
                        class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                    >
                    <button 
                        type="button" 
                        onclick="togglePasswordVisibility('password_confirmation', this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1 cursor-pointer focus:outline-none"
                        title="Tampilkan / Sembunyikan Kata Sandi"
                    >
                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Footer Card 2: Petunjuk Kata Sandi -->
    <div class="pt-2 flex items-center gap-2 text-[12px] text-slate-500 select-none">
        <i data-lucide="info" class="w-4 h-4 text-slate-400 shrink-0"></i>
        <span>Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.</span>
    </div>

</div>
