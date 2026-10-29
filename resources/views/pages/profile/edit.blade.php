@extends('layouts.app')

@php
    $breadcrumbCategory = 'Akun';
    $breadcrumbPage = 'Edit Profil';
@endphp

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10">

    <!-- 1. JUDUL HALAMAN & SUB-JUDUL -->
    <div class="space-y-1">
        <h1 class="text-[26px] font-bold text-slate-900 tracking-tight">
            Edit Profil
        </h1>
        <p class="text-[13.5px] text-slate-500">
            Kelola informasi pribadi dan keamanan akun SIApel Anda.
        </p>
    </div>

    <!-- Feedback Status Messages -->
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200/90 text-emerald-800 text-[13px] font-medium flex items-center gap-2.5 shadow-2xs">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200/90 text-red-800 text-[13px] flex items-start gap-2.5 shadow-2xs">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-bold">Terjadi kesalahan validasi data:</p>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-[12px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- 2. STRUKTUR LAYOUT KONTEN (GRID 2/3 KIRI DAN 1/3 KANAN) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 xl:gap-12 items-start">
        
        <!-- KOLOM KIRI (FORMULIR EDIT - LG:COL-SPAN-8) -->
        <div class="lg:col-span-8">
            <form method="POST" action="{{ role_route('profile.update') }}" id="profileEditForm" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Card 1: Informasi pribadi -->
                @include('pages.profile.personal-info-card')

                <!-- Card 2: Ubah kata sandi -->
                @include('pages.profile.password-change-card')

                <!-- Baris Tombol Aksi (Di bawah Card 2) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-1 select-none">
                    
                    <!-- Kiri: Teks Peringatan -->
                    <span class="text-[12.5px] text-slate-400 font-normal">
                        Periksa kembali sebelum menyimpan.
                    </span>

                    <!-- Kanan: Tombol Batal & Simpan Perubahan -->
                    <div class="flex items-center gap-2.5 justify-end">
                        <!-- Tombol Batal -->
                        <button 
                            type="button" 
                            onclick="resetProfileForm()"
                            class="px-5 py-2.5 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 font-medium text-[13px] transition-colors cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>

                        <!-- Tombol Simpan Perubahan -->
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-[13px] inline-flex items-center gap-2 shadow-xs transition-colors cursor-pointer"
                        >
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan perubahan</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <!-- KOLOM KANAN (KARTU RINGKASAN PROFIL - LG:COL-SPAN-4 DENGAN JARAK LEGA) -->
        <div class="lg:col-span-4 lg:pl-2 xl:pl-4">
            @include('pages.profile.profile-summary-card')
        </div>

    </div>

</div>

<!-- Interactive Scripts for Password Visibility Toggle & Live Sync -->
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        
        const icon = btn.querySelector('i');
        if (icon) {
            icon.setAttribute('data-lucide', isPassword ? 'eye' : 'eye-off');
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }

    function resetProfileForm() {
        if (confirm('Batalkan perubahan yang belum disimpan?')) {
            window.location.reload();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const summaryName = document.getElementById('summaryUserName');

        if (nameInput && summaryName) {
            nameInput.addEventListener('input', function() {
                summaryName.textContent = this.value.trim() || 'Nama Pengguna';
            });
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
