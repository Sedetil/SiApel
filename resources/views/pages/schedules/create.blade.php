@extends('layouts.app')

@php
    $breadcrumbCategory = 'Menu Utama';
    $breadcrumbPage = 'Buat Jadwal Khusus';
@endphp

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10">

    <!-- Header Page with Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-[26px] font-bold text-slate-900 tracking-tight">
                Buat Jadwal Khusus
            </h1>
            <p class="text-[13.5px] text-slate-500">
                Tentukan kegiatan, peserta, dan waktu absensi dalam satu pengaturan.
            </p>
        </div>

        <a 
            href="{{ route('admin.schedules.index') }}" 
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200/90 text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium text-[13px] transition-colors cursor-pointer select-none"
        >
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Jadwal</span>
        </a>
    </div>

    <!-- Container Form & Preview -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 sm:p-7 space-y-6">
        
        <!-- Header Section Form -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-[18px] font-bold text-slate-900 tracking-tight">
                    Pengaturan jadwal khusus baru
                </h2>
                <p class="text-[13px] text-slate-500 mt-0.5">
                    Lengkapi seluruh informasi wajib berikut sebelum menyimpan.
                </p>
            </div>
            <div class="text-[12px] text-slate-400 font-medium select-none sm:self-start sm:pt-1">
                * Wajib diisi
            </div>
        </div>

        <!-- Split Grid Form (2/3 Kiri dan 1/3 Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-1">
            
            <!-- Kiri (lg:col-span-8): Form Steps 1, 2, 3 -->
            <div class="lg:col-span-8">
                @include('pages.schedules.form-section')
            </div>

            <!-- Kanan (lg:col-span-4): Area Pratinjau Jadwal -->
            <div class="lg:col-span-4">
                @include('pages.schedules.preview-card')
            </div>

        </div>

        <!-- Footer Form Actions (Bagian Paling Bawah Card Form) -->
        <div class="border-t border-slate-100 pt-5 mt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 select-none">
            
            <!-- Kiri: Status bentrok -->
            <div class="flex items-center gap-2 text-emerald-600 font-medium text-[12.5px]">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                <span>Tidak ada bentrok dengan jadwal khusus lainnya.</span>
            </div>

            <!-- Kanan: 3 Tombol Aksi -->
            <div class="flex items-center gap-2.5 justify-end">
                <!-- Tombol Batal -->
                <a 
                    href="{{ route('admin.schedules.index') }}" 
                    class="px-4 py-2.5 rounded-xl border border-slate-200/90 text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium text-[13px] transition-colors cursor-pointer"
                >
                    Batal
                </a>

                <!-- Tombol Simpan Draf -->
                <button 
                    type="button" 
                    onclick="alert('Draf berhasil disimpan!')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200/90 text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium text-[13px] transition-colors cursor-pointer"
                >
                    Simpan draf
                </button>

                <!-- Tombol Simpan Jadwal -->
                <button 
                    type="button" 
                    onclick="alert('Jadwal berhasil disimpan!'); window.location.href='{{ route('admin.schedules.index') }}';"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-[13px] inline-flex items-center gap-2 shadow-xs transition-colors cursor-pointer"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan jadwal</span>
                </button>
            </div>

        </div>

    </div>

</div>

<!-- Interactive Scripts for Live Preview -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputEventName = document.getElementById('inputEventName');
        const inputStartDate = document.getElementById('inputStartDate');
        const inputStartTime = document.getElementById('inputStartTime');
        const inputEndTime = document.getElementById('inputEndTime');
        const selectLateTolerance = document.getElementById('selectLateTolerance');
        const inputLocation = document.getElementById('inputLocation');
        const selectResponsiblePerson = document.getElementById('selectResponsiblePerson');

        const previewTitle = document.getElementById('previewTitle');
        const previewDate = document.getElementById('previewDate');
        const previewTime = document.getElementById('previewTime');
        const previewTolerance = document.getElementById('previewTolerance');
        const previewLocation = document.getElementById('previewLocation');
        const previewPIC = document.getElementById('previewPIC');
        const previewParticipantScope = document.getElementById('previewParticipantScope');
        const previewParticipantDetail = document.getElementById('previewParticipantDetail');

        if (inputEventName && previewTitle) {
            inputEventName.addEventListener('input', function() {
                previewTitle.textContent = this.value.trim() || 'Judul Kegiatan';
            });
        }

        if (inputStartDate && previewDate) {
            inputStartDate.addEventListener('input', function() {
                const val = this.value.trim();
                previewDate.textContent = val || '-';

                // Sinkronisasi badge tanggal (Hari & Bulan)
                if (val) {
                    const parts = val.split(/[\s-]+/);
                    const badgeDay = document.getElementById('previewBadgeDay');
                    const badgeMonth = document.getElementById('previewBadgeMonth');
                    if (parts.length >= 2) {
                        if (badgeDay && !isNaN(parts[0])) badgeDay.textContent = parts[0];
                        if (badgeMonth) badgeMonth.textContent = parts[1].substring(0, 5).toUpperCase();
                    }
                }
            });
        }

        function updateTimePreview() {
            const start = inputStartTime ? inputStartTime.value.trim() : '07.00 WIB';
            const end = inputEndTime ? inputEndTime.value.trim() : '08.30 WIB';
            if (previewTime) previewTime.textContent = `${start} - ${end}`;
            if (selectLateTolerance && previewTolerance) {
                previewTolerance.textContent = `Toleransi keterlambatan ${selectLateTolerance.value}`;
            }
        }

        if (inputStartTime) inputStartTime.addEventListener('input', updateTimePreview);
        if (inputEndTime) inputEndTime.addEventListener('input', updateTimePreview);
        if (selectLateTolerance) selectLateTolerance.addEventListener('change', updateTimePreview);

        if (inputLocation && previewLocation) {
            inputLocation.addEventListener('input', function() {
                previewLocation.textContent = this.value.trim() || '-';
            });
        }

        if (selectResponsiblePerson && previewPIC) {
            selectResponsiblePerson.addEventListener('change', function() {
                const cleanName = this.value.split('·')[0].trim();
                previewPIC.textContent = cleanName || this.value;
            });
        }

        const radioScopeButtons = document.querySelectorAll('input[name="cakupan_peserta"]');
        radioScopeButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                radioScopeButtons.forEach(r => {
                    const label = r.closest('label');
                    const dot = label.querySelector('.w-2.h-2');
                    const circle = label.querySelector('.w-4.h-4');
                    const text = label.querySelector('span');
                    if (r.checked) {
                        label.className = "relative flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-blue-600 bg-white cursor-pointer transition-all shadow-2xs select-none group";
                        circle.className = "w-4 h-4 rounded-full border border-blue-600 flex items-center justify-center shrink-0";
                        dot.className = "w-2 h-2 rounded-full bg-blue-600";
                        text.className = "text-[12.5px] font-semibold text-blue-600";
                    } else {
                        label.className = "relative flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50/70 cursor-pointer transition-all shadow-2xs select-none group";
                        circle.className = "w-4 h-4 rounded-full border border-slate-300 group-hover:border-slate-400 flex items-center justify-center shrink-0";
                        dot.className = "w-2 h-2 rounded-full bg-transparent";
                        text.className = "text-[12.5px] font-medium text-slate-700 group-hover:text-slate-900";
                    }
                });

                if (previewParticipantScope) {
                    if (this.value === 'all') {
                        previewParticipantScope.textContent = 'Seluruh mahasiswa';
                        if (previewParticipantDetail) previewParticipantDetail.textContent = 'Semua angkatan · 3.329 mahasiswa';
                    } else if (this.value === 'jurusan') {
                        previewParticipantScope.textContent = 'Jurusan / program studi';
                        if (previewParticipantDetail) previewParticipantDetail.textContent = 'Teknologi Informasi & Pertanian';
                    } else {
                        previewParticipantScope.textContent = 'Mahasiswa tertentu';
                        if (previewParticipantDetail) previewParticipantDetail.textContent = 'Daftar mahasiswa terpilih';
                    }
                }
            });
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
