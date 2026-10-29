@extends('layouts.guest')

@section('content')
<div class="w-full max-w-md">

    <!-- Card Container (Radius 12px, Padding 24px) -->
    <div class="bg-surface border border-border rounded-lg shadow-sm p-6 sm:p-8">

        <!-- Header / Logo Brand -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-2">
                <img src="{{ asset('static/img/logo.png') }}" alt="Logo POLINELA" class="w-16 h-16 object-contain drop-shadow-sm">
            </div>
            
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                SIABSEN POLINELA
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Sistem Informasi Absensi Apel Berbasis Lokasi
            </p>

            <!-- GPS Verification Notice Badge (Radius 6px) -->
            <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Validasi GPS Geofencing Aktif</span>
            </div>
        </div>

        <!-- Feedback Messages -->
        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-md bg-success/10 border border-success/30 text-success text-[13px] font-medium flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-md bg-error/10 border border-error/30 text-error text-[13px] flex items-start gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-bold">Gagal Masuk</p>
                    <ul class="list-disc list-inside mt-0.5 space-y-0.5 text-[12px] opacity-90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Identifier: NPM / NIP / Email -->
            <div>
                <label for="identifier" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    NPM / NIP / Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-text-secondary">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="text" 
                        id="identifier" 
                        name="identifier" 
                        value="{{ old('identifier') }}" 
                        required 
                        autofocus
                        placeholder="Masukkan NPM mahasiswa atau NIP pembina"
                        class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-md text-[14px] text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    >
                </div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider">
                        Kata Sandi
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[12px] font-medium text-primary hover:text-primary-dark transition-colors">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-text-secondary">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-10 pr-10 py-2.5 bg-surface border border-border rounded-md text-[14px] text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    >
                    <button 
                        type="button" 
                        id="toggle-password" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-text-secondary hover:text-text-primary transition-colors focus:outline-none"
                        aria-label="Tampilkan kata sandi"
                    >
                        <i id="eye-icon" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        class="w-4 h-4 rounded text-primary border-border focus:ring-primary focus:ring-offset-0 transition-colors"
                    >
                    <span class="text-[13px] text-text-secondary">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button (Primary Action: #2D56FF, Radius 8px) -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark active:bg-primary-dark text-white text-[14px] font-semibold py-2.5 px-4 rounded-md transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                    <span>Masuk ke Sistem</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>

        <!-- Role Information Pills (Radius 6px) -->
        <div class="mt-6 pt-5 border-t border-border">
            <p class="text-[11px] font-bold text-text-secondary uppercase tracking-wider mb-2 text-center">
                Portal Akses Pengguna
            </p>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="p-2 rounded-sm bg-background border border-border/80">
                    <span class="block text-[11px] font-bold text-text-primary">Mahasiswa</span>
                    <span class="block text-[10px] text-text-secondary">Absensi Mandiri</span>
                </div>
                <div class="p-2 rounded-sm bg-background border border-border/80">
                    <span class="block text-[11px] font-bold text-text-primary">Pembina</span>
                    <span class="block text-[10px] text-text-secondary">Validasi Kelas</span>
                </div>
                <div class="p-2 rounded-sm bg-background border border-border/80">
                    <span class="block text-[11px] font-bold text-text-primary">Admin</span>
                    <span class="block text-[10px] text-text-secondary">Kelola Master</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Security & Institutional Note -->
    <div class="mt-6 text-center">
        <p class="text-[12px] text-text-secondary flex items-center justify-center gap-1.5">
            <i data-lucide="shield-check" class="w-4 h-4 text-success"></i>
            <span>Terkoneksi aman dengan server Polinela</span>
        </p>
    </div>

</div>

@push('scripts')
<script>
    // Toggle show/hide password
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (toggleBtn && passwordInput && eyeIcon) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            // Toggle icon path
            if (isPassword) {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        });
    }
</script>
@endpush
@endsection
