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
                Sistem Absensi Apel Berbasis RFID
            </p>

            <!-- Status Badge (Radius 6px) -->
            <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>Sistem Terintegrasi Reader RFID</span>
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

            <!-- Identifier: NPM / NIP / Username / Email -->
            <div>
                <label for="identifier" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    Username / Email / NIP
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
                        placeholder="Contoh: admin atau timdis1"
                        class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-md text-[14px] text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    >
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    Kata Sandi
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-text-secondary">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Masukkan kata sandi akun"
                        class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-md text-[14px] text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                    >
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
                    <span class="text-[13px] text-text-secondary">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button (Primary #2D56FF, Radius 8px) -->
            <button 
                type="submit" 
                class="w-full py-2.5 px-4 bg-primary hover:bg-primary-dark text-white font-medium text-[14px] rounded-md transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 flex items-center justify-center gap-2"
            >
                <span>Masuk ke Portal</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <!-- Quick Demo Credentials Box -->
        <div class="mt-6 pt-5 border-t border-border/80">
            <p class="text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-2 text-center">Pilihan Cepat Login Uji Coba</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <button type="button" onclick="fillCreds('admin', 'password123')" class="px-2.5 py-2 text-left rounded-sm border border-border bg-background hover:bg-primary/5 transition-colors text-[11px]">
                    <span class="font-bold text-text-primary block">Admin Utama</span>
                    <span class="text-text-secondary text-[10px] block">Semua Jurusan</span>
                    <span class="font-mono text-[9.5px] text-primary">admin</span>
                </button>
                <button type="button" onclick="fillCreds('timdis_mi', 'password123')" class="px-2.5 py-2 text-left rounded-sm border border-blue-200 bg-blue-50/40 hover:bg-blue-50 transition-colors text-[11px]">
                    <span class="font-bold text-blue-800 block">Timdis MI</span>
                    <span class="text-slate-500 text-[10px] block">Kelas MI 3A (2024)</span>
                    <span class="font-mono text-[9.5px] text-primary">timdis_mi</span>
                </button>
                <button type="button" onclick="fillCreds('timdis_trpl', 'password123')" class="px-2.5 py-2 text-left rounded-sm border border-emerald-200 bg-emerald-50/40 hover:bg-emerald-50 transition-colors text-[11px]">
                    <span class="font-bold text-emerald-800 block">Timdis TRPL</span>
                    <span class="text-slate-500 text-[10px] block">Kelas TRPL 1B (2025)</span>
                    <span class="font-mono text-[9.5px] text-emerald-600">timdis_trpl</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function fillCreds(user, pass) {
        document.getElementById('identifier').value = user;
        document.getElementById('password').value = pass;
    }
</script>
@endsection
