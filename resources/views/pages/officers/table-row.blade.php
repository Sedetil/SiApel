@props(['officer'])

<tr 
    class="officer-row hover:bg-slate-50/70 transition-colors group"
    data-name="{{ strtolower($officer['name']) }}"
    data-username="{{ strtolower($officer['username']) }}"
    data-role="{{ $officer['role'] }}"
    data-assignment="{{ $officer['assignment'] }}"
    data-status="{{ $officer['status'] }}"
>
    <!-- Kolom Checkbox -->
    <td class="py-3.5 px-3 text-center">
        <input 
            type="checkbox" 
            class="officer-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer transition-colors"
        >
    </td>

    <!-- Kolom Anggota Timdis: Nama & Email -->
    <td class="py-3.5 px-4 min-w-[200px]">
        <div class="font-bold text-[13.5px] text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
            {{ $officer['name'] }}
        </div>
        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
            {{ $officer['email'] }}
        </div>
    </td>

    <!-- Kolom Username -->
    <td class="py-3.5 px-4 text-[13px] text-slate-700 font-medium">
        {{ $officer['username'] }}
    </td>

    <!-- Kolom Nomor Telepon -->
    <td class="py-3.5 px-4 text-[13px] text-slate-700 font-medium">
        {{ $officer['phone'] }}
    </td>

    <!-- Kolom Peran & Penugasan -->
    <td class="py-3.5 px-4 min-w-[170px]">
        <div class="font-bold text-[13.5px] text-slate-900 leading-snug">
            {{ $officer['role'] }}
        </div>
        <div class="text-[12px] text-slate-400 font-normal mt-0.5">
            {{ $officer['assignment'] }}
        </div>
    </td>

    <!-- Kolom Status Akun: Pill dengan Dot Sesuai Varian -->
    <td class="py-3.5 px-4 whitespace-nowrap">
        @if($officer['status'] === 'Aktif')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                <span>Aktif</span>
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block shrink-0"></span>
                <span>Nonaktif</span>
            </span>
        @endif
    </td>

    <!-- Kolom Aksi: Deretan 3 Tombol Kotak Ber-border -->
    <td class="py-3.5 px-4 text-center">
        <div class="flex items-center justify-center gap-1.5">
            
            <!-- Tombol 1: Ikon Pensil (Edit) -->
            <button 
                type="button" 
                onclick="openEditOfficerModal({{ json_encode($officer) }})"
                class="w-8 h-8 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-500 hover:text-slate-800 transition-colors shadow-2xs cursor-pointer"
                title="Edit Data {{ $officer['name'] }}"
            >
                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
            </button>

            <!-- Tombol 2: Ikon Kunci (Reset Password / Akses) -->
            <button 
                type="button" 
                onclick="openResetPasswordModal({{ json_encode($officer) }})"
                class="w-8 h-8 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-500 hover:text-slate-800 transition-colors shadow-2xs cursor-pointer"
                title="Reset Akses / Password"
            >
                <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
            </button>

            <!-- Tombol 3: Ikon User Toggle (Silang Merah jika Aktif / Ceklis Hijau jika Nonaktif) -->
            @if($officer['status'] === 'Aktif')
                <button 
                    type="button" 
                    onclick="openToggleStatusModal({{ json_encode($officer) }})"
                    class="w-8 h-8 rounded-lg border border-slate-200/90 bg-white hover:bg-red-50 flex items-center justify-center text-red-500 hover:text-red-700 transition-colors shadow-2xs cursor-pointer"
                    title="Nonaktifkan Akun"
                >
                    <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                </button>
            @else
                <button 
                    type="button" 
                    onclick="openToggleStatusModal({{ json_encode($officer) }})"
                    class="w-8 h-8 rounded-lg border border-slate-200/90 bg-white hover:bg-emerald-50 flex items-center justify-center text-emerald-600 hover:text-emerald-700 transition-colors shadow-2xs cursor-pointer"
                    title="Aktifkan Akun"
                >
                    <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                </button>
            @endif

        </div>
    </td>
</tr>
