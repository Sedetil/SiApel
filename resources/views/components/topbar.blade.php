@props([
    'category' => null,
    'title' => null,
    'userRole' => null,
    'userInitials' => null,
])

@php
    // Deteksi otomatis kategori breadcrumb berdasarkan route aktif jika tidak diisi manual
    if (!$category) {
        if (request()->routeIs('*permission-requests*')) {
            $category = 'Verifikasi & Approval';
        } elseif (request()->routeIs('*participants*') || request()->routeIs('*officers*')) {
            $category = 'User Management';
        } elseif (request()->routeIs('*ekuliah*') || request()->routeIs('*audit-logs*')) {
            $category = 'Pengaturan Sistem';
        } elseif (request()->routeIs('*profile*')) {
            $category = 'Akun';
        } else {
            $category = 'Menu Utama';
        }
    }

    // Deteksi otomatis judul halaman aktif berdasarkan route jika tidak diisi manual
    if (!$title) {
        if (request()->routeIs('*dashboard*')) {
            $title = 'Dashboard';
        } elseif (request()->routeIs('*attendance.history*')) {
            $title = 'Riwayat Absensi';
        } elseif (request()->routeIs('*permission-requests*')) {
            $title = 'Izin & Sakit';
        } elseif (request()->routeIs('*participants.create')) {
            $title = 'Tambah Mahasiswa';
        } elseif (request()->routeIs('*participants.show')) {
            $title = 'Detail Mahasiswa';
        } elseif (request()->routeIs('*participants*') || request()->routeIs('*mahasiswa*')) {
            $title = 'Data Mahasiswa';
        } elseif (request()->routeIs('*officers*') || request()->routeIs('*timdis*')) {
            $title = 'Data Timdis';
        } elseif (request()->routeIs('*schedules*') || request()->routeIs('*jadwal*')) {
            $title = 'Atur Jadwal Khusus';
        } elseif (request()->routeIs('*ekuliah*')) {
            $title = 'Integrasi E-Kuliah';
        } elseif (request()->routeIs('*audit-logs*')) {
            $title = 'Audit Trail';
        } elseif (request()->routeIs('*profile*')) {
            $title = 'Edit Profil';
        } elseif (request()->routeIs('*reports*')) {
            $title = 'Rekap Laporan';
        } else {
            $title = 'Dashboard';
        }
    }

    // Format peran pengguna dan inisial avatar
    if (!$userRole) {
        $rawRole = auth()->user()->role ?? 'Admin';
        $userRole = $rawRole === 'petugas' ? 'Timdis' : ucfirst($rawRole);
    }

    if (!$userInitials) {
        $name = auth()->user()->name ?? 'Admin';
        $userInitials = strtoupper(substr($name, 0, 1));
    }
@endphp

<!-- REUSABLE TOPBAR COMPONENT -->
<header {{ $attributes->merge(['class' => 'min-h-[60px] py-3.5 px-6 sm:px-8 bg-white border-b border-slate-200/90 flex items-center justify-between sticky top-0 z-30 transition-all shadow-[0_1px_2px_rgba(0,0,0,0.02)]']) }}>
    
    <!-- Left: Breadcrumb & Sidebar Toggle -->
    <div class="flex items-center gap-3">
        <!-- Sidebar Toggle (Mobile Drawer & Desktop Mini Sidebar) -->
        <button 
            type="button" 
            id="sidebar-toggle-btn" 
            class="p-1.5 -ml-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer"
            aria-label="Toggle Sidebar Navigation"
            title="Tutup / Buka Sidebar"
        >
            <i data-lucide="panel-left" class="w-4 h-4"></i>
        </button>

        <!-- Dynamic Breadcrumb Navigation -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs select-none">
            <a href="{{ role_route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors flex items-center" title="Kembali ke Dashboard">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
            </a>

            <span class="text-slate-300 flex items-center">
                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            </span>

            <span class="text-slate-500 hover:text-slate-700 transition-colors font-medium">
                {{ $category }}
            </span>

            <span class="text-slate-300 flex items-center">
                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            </span>

            <strong class="text-slate-800 font-semibold text-[13px] tracking-tight">
                {{ $title }}
            </strong>
        </nav>
    </div>

    <!-- Right: User Role & Avatar -->
    <div class="flex items-center gap-2.5">
        <span class="text-xs text-slate-500 font-medium">
            {{ $userRole }}
        </span>
        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center text-xs shadow-sm ring-2 ring-white">
            {{ $userInitials }}
        </div>
    </div>

</header>
