<!-- CARD 1: INFORMASI PRIBADI -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-5">
    
    <!-- Header Card -->
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
            <i data-lucide="user" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-[16px] font-bold text-slate-900 tracking-tight leading-snug">
                Informasi pribadi
            </h2>
            <p class="text-[12.5px] text-slate-500 mt-0.5 leading-snug">
                Perbarui nama dan kontak yang dapat dihubungi.
            </p>
        </div>
    </div>

    <!-- Divider Line -->
    <div class="border-b border-slate-100"></div>

    <!-- Form Fields -->
    <div class="space-y-4 pt-1">
        
        <!-- Field 1 (Lebar Penuh): Nama lengkap -->
        <div class="space-y-1.5">
            <label for="name" class="block text-[12px] font-semibold text-slate-700">
                Nama lengkap
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', 'I Komang Artayasa') }}"
                required
                placeholder="Masukkan nama lengkap Anda"
                class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
            >
            @error('name')
                <p class="text-[11.5px] text-red-500 font-medium mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field 2 & 3 (Grid 2 Kolom): Email & Nomor Telepon -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Kiri: Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-[12px] font-semibold text-slate-700">
                    Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', '') }}"
                    placeholder="Masukkan alamat email"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                >
                <span class="block text-[11.5px] text-slate-400 font-normal">
                    Belum diisi
                </span>
                @error('email')
                    <p class="text-[11.5px] text-red-500 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kanan: Nomor telepon -->
            <div class="space-y-1.5">
                <label for="phone" class="block text-[12px] font-semibold text-slate-700">
                    Nomor telepon
                </label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', '') }}"
                    placeholder="Masukkan nomor telepon"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-2xs font-normal"
                >
                <span class="block text-[11.5px] text-slate-400 font-normal">
                    Belum diisi
                </span>
                @error('phone')
                    <p class="text-[11.5px] text-red-500 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

    </div>

</div>
