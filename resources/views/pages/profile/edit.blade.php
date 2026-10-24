@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Edit Profil Akun
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Kelola informasi biodata akun, ubah kata sandi keamanan, dan perbarui foto profil Anda.
            </p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-bold bg-blue-50 text-blue-700 border border-blue-200 w-fit">
            <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
            <span>Role: {{ ucfirst(auth()->user()->role) }}</span>
        </span>
    </div>

    <!-- Feedback Status Messages -->
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-medium flex items-center gap-2.5 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('password_status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-medium flex items-center gap-2.5 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('password_status') }}</span>
        </div>
    @endif

    @if (session('avatar_status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-medium flex items-center gap-2.5 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('avatar_status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-[13px] flex items-start gap-2.5 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-bold">Terjadi Kesalahan Validasi</p>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-[12px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Upload Foto Profil Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4 block w-full text-left">
                    Foto Profil
                </span>

                <div class="relative group">
                    <img id="avatar-preview" 
                         src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}" 
                         class="w-28 h-28 rounded-2xl object-cover border-2 border-slate-200/80 shadow-md">
                    <div class="absolute inset-0 bg-slate-900/40 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                        <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                    </div>
                </div>

                <h3 class="text-[16px] font-bold text-slate-900 mt-4 leading-tight">{{ $user->name }}</h3>
                <p class="text-[12px] font-mono text-slate-500 mt-0.5">{{ '@' . $user->username }}</p>

                <!-- Form Upload Avatar -->
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="w-full mt-5 pt-4 border-t border-slate-100 space-y-3">
                    @csrf
                    <div>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        <label for="avatar" class="w-full inline-flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-semibold text-[13px] py-2 px-3 rounded-lg cursor-pointer transition-colors shadow-xs">
                            <i data-lucide="upload" class="w-4 h-4 text-slate-500"></i>
                            <span>Pilih Foto Baru</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mt-1.5">Format JPG, PNG, WEBP. Maks 2MB.</p>
                    </div>

                    <button type="submit" id="save-avatar-btn" class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-semibold text-[13px] py-2 px-3 rounded-lg transition-colors shadow-sm hidden">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan Foto</span>
                    </button>
                </form>

                <!-- User Meta Details -->
                <div class="w-full mt-4 pt-4 border-t border-slate-100 text-left text-[12px] space-y-2 text-slate-600">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Unit / Prodi:</span>
                        <strong class="text-slate-800">{{ $user->study_program ?? '-' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Status Akun:</span>
                        <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Terdaftar Sejak:</span>
                        <span class="text-slate-800 font-mono">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Data Profil & Ganti Password (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card 1: Data Profil -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Data Profil</h2>
                        <p class="text-[12px] text-slate-500 mt-0.5">Informasi identitas akun yang digunakan dalam operasional absensi.</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                                   class="w-full font-mono text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>

                        <!-- Nomor Telepon -->
                        <div>
                            <label for="phone" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nomor Telepon / WA
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789"
                                   class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <!-- Program Studi / Unit Kerja -->
                    <div>
                        <label for="study_program" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Program Studi / Unit Kerja
                        </label>
                        <input type="text" id="study_program" name="study_program" value="{{ old('study_program', $user->study_program) }}" placeholder="Contoh: Manajemen Informatika atau Pusat Komputer"
                               class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-semibold text-[13px] px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Ganti Password -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-[16px] font-bold text-slate-900 leading-tight">Ganti Kata Sandi</h2>
                        <p class="text-[12px] text-slate-500 mt-0.5">Pastikan kata sandi Anda kuat untuk mengamankan akses akun.</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="key-round" class="w-4 h-4"></i>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kata Sandi Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="current_password" name="current_password" required placeholder="Masukkan kata sandi saat ini"
                               class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                                   class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-[12px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Ulangi Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi kata sandi baru"
                                   class="w-full text-[13.5px] px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-[13px] px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
                document.getElementById('save-avatar-btn').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
