@extends('layouts.app')

@php
    $breadcrumbCategory = 'Menu Utama';
    $breadcrumbPage = 'Data Timdis';

    // 10 Baris Mock Data persis seperti pada desain UI mockup
    $mockOfficers = [
        [
            'id' => 1,
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.f@example.com',
            'username' => 'timdis.ahmad',
            'phone' => '0812 0000 0101',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GSG',
            'status' => 'Aktif',
        ],
        [
            'id' => 2,
            'name' => 'Nabila Rahmawati',
            'email' => 'nabila.r@example.com',
            'username' => 'timdis.nabila',
            'phone' => '0812 0000 0102',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GSG',
            'status' => 'Aktif',
        ],
        [
            'id' => 3,
            'name' => 'Rizky Firmansyah',
            'email' => 'rizky.f@example.com',
            'username' => 'timdis.rizky',
            'phone' => '0812 0000 0103',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GSG',
            'status' => 'Aktif',
        ],
        [
            'id' => 4,
            'name' => 'Salsabila Putri',
            'email' => 'salsa.p@example.com',
            'username' => 'timdis.salsa',
            'phone' => '0812 0000 0104',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GKB',
            'status' => 'Aktif',
        ],
        [
            'id' => 5,
            'name' => 'Dimas Setiawan',
            'email' => 'dimas.s@example.com',
            'username' => 'timdis.dimas',
            'phone' => '0812 0000 0105',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GKB',
            'status' => 'Aktif',
        ],
        [
            'id' => 6,
            'name' => 'Fitri Handayani',
            'email' => 'fitri.h@example.com',
            'username' => 'timdis.fitri',
            'phone' => '0812 0000 0106',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GSG',
            'status' => 'Nonaktif',
        ],
        [
            'id' => 7,
            'name' => 'Galih Prakoso',
            'email' => 'galih.p@example.com',
            'username' => 'timdis.galih',
            'phone' => '0812 0000 0107',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GKB',
            'status' => 'Aktif',
        ],
        [
            'id' => 8,
            'name' => 'Laras Wulandari',
            'email' => 'laras.w@example.com',
            'username' => 'timdis.laras',
            'phone' => '0812 0000 0108',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GKB',
            'status' => 'Aktif',
        ],
        [
            'id' => 9,
            'name' => 'Reza Adi Nugraha',
            'email' => 'reza.a@example.com',
            'username' => 'timdis.reza',
            'phone' => '0812 0000 0109',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GSG',
            'status' => 'Nonaktif',
        ],
        [
            'id' => 10,
            'name' => 'Tiara Maharani',
            'email' => 'tiara.m@example.com',
            'username' => 'timdis.tiara',
            'phone' => '0812 0000 0110',
            'role' => 'Anggota',
            'assignment' => 'Lapangan GSG',
            'status' => 'Aktif',
        ],
    ];
@endphp

@section('content')
<div class="space-y-5 max-w-[1240px] mx-auto pb-8">

    <!-- 2. JUDUL HALAMAN & TOMBOL AKSI UTAMA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1 pb-1">
        <!-- Kiri: Heading & Sub-heading -->
        <div>
            <h1 class="text-[26px] font-bold text-slate-900 tracking-tight leading-tight">
                Data Timdis
            </h1>
            <p class="text-[13.5px] text-slate-500 mt-0.5">
                Kelola anggota tim disiplin, penugasan, dan akses akun SIApel.
            </p>
        </div>

        <!-- Kanan: Tombol + Tambah anggota -->
        <div>
            <button 
                type="button" 
                onclick="openCreateOfficerModal()"
                class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-[13px] font-medium shadow-xs transition-colors cursor-pointer"
                title="Tambah Anggota Timdis Baru"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah anggota</span>
            </button>
        </div>
    </div>

    <!-- 3. CARD UTAMA (KONTAINER TABEL & FILTER) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-5">
        
        <!-- Bagian Atas Card (Header) -->
        @include('pages.officers.header-card')

        <!-- Baris Pencarian & Filter Dropdowns -->
        @include('pages.officers.filter-bar')

        <!-- 4. Tabel Data Timdis -->
        @include('pages.officers.table-list', ['officers' => $mockOfficers])

        <!-- 5. Footer Pagination & Rows Per Page -->
        @include('pages.officers.footer-pagination')

    </div>

    <!-- 6. CATATAN KAKI (DISCLAIMER DI LUAR CARD) -->
    <div class="flex items-center gap-2 text-[12.5px] text-slate-400 px-1 pt-1">
        <i data-lucide="info" class="w-4 h-4 text-slate-400 shrink-0"></i>
        <span>Data pada halaman ini adalah contoh, bukan data institusi terverifikasi.</span>
    </div>

</div>

<!-- Interactive Action Modals -->
@include('pages.officers.modals')

@push('scripts')
<script>
    let activeOfficerData = null;

    // 1. Checkbox Select All Toggle
    function toggleSelectAllOfficers(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.officer-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const childCheckboxes = document.querySelectorAll('.officer-checkbox');
        const master = document.getElementById('selectAllOfficersCheckbox');
        
        childCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const total = childCheckboxes.length;
                const checked = document.querySelectorAll('.officer-checkbox:checked').length;
                if (master) {
                    master.checked = total > 0 && total === checked;
                    master.indeterminate = checked > 0 && checked < total;
                }
            });
        });
    });

    // 2. Real-Time Client-Side Filter
    function filterOfficersTable() {
        const searchVal = document.getElementById('searchOfficerInput').value.toLowerCase().trim();
        const roleVal = document.getElementById('filterRole').value;
        const assignmentVal = document.getElementById('filterAssignment').value;
        const statusVal = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('.officer-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const username = row.getAttribute('data-username') || '';
            const role = row.getAttribute('data-role') || '';
            const assignment = row.getAttribute('data-assignment') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = !searchVal || name.includes(searchVal) || username.includes(searchVal);
            const matchesRole = !roleVal || role === roleVal;
            const matchesAssignment = !assignmentVal || assignment === assignmentVal;
            const matchesStatus = !statusVal || status === statusVal;

            if (matchesSearch && matchesRole && matchesAssignment && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noOfficerResults');
        const rangeText = document.getElementById('officerVisibleRange');

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            if (rangeText) rangeText.textContent = '0–0';
        } else {
            noResults.classList.add('hidden');
            if (rangeText) rangeText.textContent = `1–${visibleCount}`;
        }
    }

    // 3. Modal Triggers
    function openCreateOfficerModal() {
        document.getElementById('createOfficerModal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeCreateOfficerModal() {
        document.getElementById('createOfficerModal').classList.add('hidden');
    }

    function openEditOfficerModal(officer) {
        if (!officer) return;
        activeOfficerData = officer;

        document.getElementById('edit_name').value = officer.name || '';
        document.getElementById('edit_username').value = officer.username || '';
        document.getElementById('edit_phone').value = officer.phone || '';
        document.getElementById('edit_email').value = officer.email || '';
        document.getElementById('edit_role').value = officer.role || 'Anggota';
        document.getElementById('edit_assignment').value = officer.assignment || 'Lapangan GSG';

        document.getElementById('editOfficerModal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeEditOfficerModal() {
        document.getElementById('editOfficerModal').classList.add('hidden');
    }

    function saveEditOfficer() {
        alert('Data anggota ' + (activeOfficerData ? activeOfficerData.name : '') + ' berhasil diperbarui!');
        closeEditOfficerModal();
    }

    function openResetPasswordModal(officer) {
        if (!officer) return;
        activeOfficerData = officer;

        document.getElementById('resetTargetName').textContent = officer.name;
        document.getElementById('resetTargetUsername').textContent = officer.username;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }

    function confirmResetPassword() {
        alert('Password untuk ' + (activeOfficerData ? activeOfficerData.name : '') + ' berhasil direset!');
        closeResetPasswordModal();
    }

    function openToggleStatusModal(officer) {
        if (!officer) return;
        activeOfficerData = officer;

        const isCurrentlyActive = officer.status === 'Aktif';
        const title = document.getElementById('statusModalTitle');
        const message = document.getElementById('statusModalMessage');
        const iconBox = document.getElementById('statusModalIconContainer');
        const confirmBtn = document.getElementById('statusModalConfirmBtn');

        if (isCurrentlyActive) {
            title.textContent = 'Konfirmasi Nonaktifkan Akun';
            message.textContent = 'Apakah Anda yakin ingin menonaktifkan akun anggota timdis ' + officer.name + '? Anggota tidak akan dapat masuk ke sistem absensi selama akun berstatus nonaktif.';
            iconBox.className = 'w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center';
            iconBox.innerHTML = '<i data-lucide="user-x" class="w-4 h-4"></i>';
            confirmBtn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors cursor-pointer shadow-xs';
            confirmBtn.textContent = 'Nonaktifkan';
        } else {
            title.textContent = 'Konfirmasi Aktifkan Akun';
            message.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali akun anggota timdis ' + officer.name + '? Anggota akan dapat kembali masuk dan menjalankan tugas verifikasi absensi.';
            iconBox.className = 'w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center';
            iconBox.innerHTML = '<i data-lucide="user-check" class="w-4 h-4"></i>';
            confirmBtn.className = 'px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors cursor-pointer shadow-xs';
            confirmBtn.textContent = 'Aktifkan Akun';
        }

        document.getElementById('toggleStatusModal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeToggleStatusModal() {
        document.getElementById('toggleStatusModal').classList.add('hidden');
    }

    function confirmToggleStatus() {
        alert('Status akun ' + (activeOfficerData ? activeOfficerData.name : '') + ' berhasil diubah!');
        closeToggleStatusModal();
    }
</script>
@endpush
@endsection
