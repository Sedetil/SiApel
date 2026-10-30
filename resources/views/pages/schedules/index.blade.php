@extends('layouts.app')

@php
    $breadcrumbCategory = 'Menu Utama';
    $breadcrumbPage = 'Atur Jadwal Khusus';
@endphp

@section('content')
<div class="space-y-7 max-w-7xl mx-auto pb-10">

    <!-- 1. JUDUL HALAMAN & TOMBOL AKSI UTAMA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-[26px] font-bold text-slate-900 tracking-tight">
                Atur Jadwal Khusus
            </h1>
            <p class="text-[13.5px] text-slate-500">
                Kelola waktu dan peserta absensi untuk kegiatan khusus mahasiswa Polinela.
            </p>
        </div>

        <!-- Tombol Aksi: Buat Jadwal Khusus (Navigasi ke Halaman Pembuatan) -->
        <div class="flex items-center gap-2.5">
            <a 
                href="{{ route('admin.schedules.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-[13px] font-medium shadow-xs transition-all cursor-pointer select-none"
                title="Buka Halaman Buat Jadwal Khusus"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Jadwal Khusus</span>
            </a>
        </div>
    </div>

    <!-- 2. BAGIAN 1: RINGKASAN CARD (GRID 1X4) -->
    @include('pages.schedules.summary-cards')

    <!-- 3. BAGIAN 2: TABEL "DAFTAR JADWAL KHUSUS" (CARD LEBAR) -->
    <div class="pt-4 sm:pt-6">
        @include('pages.schedules.schedule-table')
    </div>



</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
