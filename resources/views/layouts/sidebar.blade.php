<!-- SIDEBAR NAVIGATION -->
<aside id="app-sidebar" class="w-60 bg-white border-r border-slate-200/90 flex flex-col justify-between fixed top-0 bottom-0 left-0 z-40 transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 select-none">
    
    <!-- Top Area: Logo, Profile, and Nav Links -->
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-1 pt-1 group" id="sidebar-logo" title="SiApel POLINELA">
            <img src="{{ asset('static/img/logo.png') }}" alt="Logo POLINELA" class="w-9 h-9 object-contain shrink-0 group-hover:scale-105 transition-transform">
            <div class="flex flex-col leading-tight sidebar-label overflow-hidden">
                <span class="font-extrabold text-[17px] tracking-tight text-slate-900 whitespace-nowrap">SiApel</span>
                <span class="text-[9.5px] font-medium text-slate-400 whitespace-nowrap">Sistem Absensi Polinela</span>
            </div>
        </a>

        <!-- User Profile Card -->
        <a href="{{ route('profile.edit') }}" class="border border-slate-200/80 rounded-xl p-2.5 bg-slate-50/50 flex items-center justify-between hover:bg-slate-50 transition-colors group sidebar-user-card" title="Edit Profil: {{ auth()->user()->name }}">
            <div class="flex items-center gap-2.5 min-w-0">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-lg object-cover shrink-0 border border-slate-200">
                @else
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0 sidebar-label overflow-hidden">
                    <div class="font-bold text-[12px] text-slate-800 truncate group-hover:text-primary transition-colors" title="{{ auth()->user()->name }}">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize truncate">
                        {{ (auth()->user()->role ?? 'Admin') === 'petugas' ? 'Timdis' : ucfirst(auth()->user()->role ?? 'Admin') }}
                    </div>
                </div>
            </div>
            <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1 group-hover:text-slate-600 sidebar-label"></i>
        </a>

        <!-- Section 1: MENU UTAMA -->
        <div>
            <div class="sidebar-section-header px-2.5 py-1 text-[9.5px] font-bold text-slate-400 uppercase tracking-wider">
                MENU UTAMA
            </div>
            <div class="sidebar-section-divider hidden h-px bg-slate-200 my-2"></div>
            <div class="mt-1 space-y-0.5">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100 shadow-[0_1px_2px_rgba(29,99,220,0.06)]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Dashboard">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span class="sidebar-label truncate">Dashboard</span>
                </a>

                <!-- Riwayat Absensi -->
                <a href="{{ route('attendance.history') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('attendance.history*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Riwayat Absensi">
                    <i data-lucide="history" class="w-4 h-4 shrink-0 {{ request()->routeIs('attendance.history*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span class="sidebar-label truncate">Riwayat Absensi</span>
                </a>
            </div>
        </div>

        <!-- Section 2: VERIFIKASI & APPROVAL -->
        <div>
            <div class="sidebar-section-header px-2.5 py-1 text-[9.5px] font-bold text-slate-400 uppercase tracking-wider">
                VERIFIKASI & APPROVAL
            </div>
            <div class="sidebar-section-divider hidden h-px bg-slate-200 my-2"></div>
            <div class="mt-1 space-y-0.5">
                <!-- Izin & Sakit -->
                <a href="{{ route('permission-requests.index') }}" class="sidebar-nav-link relative flex items-center justify-between px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('permission-requests*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100 shadow-[0_1px_2px_rgba(29,99,220,0.06)]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Izin & Sakit">
                    <div class="flex items-center gap-3 min-w-0">
                        <i data-lucide="clipboard-check" class="w-4 h-4 shrink-0 {{ request()->routeIs('permission-requests*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Izin & Sakit</span>
                    </div>
                    @php
                        $pendingPermsCount = \Illuminate\Support\Facades\Schema::hasTable('permission_requests')
                            ? \App\Models\PermissionRequest::where('status', 'menunggu')->count()
                            : 0;
                    @endphp
                    @if($pendingPermsCount > 0)
                        <span class="sidebar-badge inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700">
                            {{ $pendingPermsCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Section 3: USER MANAGEMENT -->
        <div>
            <div class="sidebar-section-header px-2.5 py-1 text-[9.5px] font-bold text-slate-400 uppercase tracking-wider">
                USER MANAGEMENT
            </div>
            <div class="sidebar-section-divider hidden h-px bg-slate-200 my-2"></div>
            <div class="mt-1 space-y-0.5">
                <!-- Mahasiswa -->
                <a href="{{ route('participants.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('participants*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Mahasiswa">
                    <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0 {{ request()->routeIs('participants*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span class="sidebar-label truncate">Mahasiswa</span>
                </a>

                <!-- Timdis -->
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('officers.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('officers*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Timdis">
                        <i data-lucide="users" class="w-4 h-4 shrink-0 {{ request()->routeIs('officers*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Timdis</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Section 4: PENGATURAN SISTEM -->
        @if(auth()->check() && auth()->user()->isAdmin())
            <div>
                <div class="sidebar-section-header px-2.5 py-1 text-[9.5px] font-bold text-slate-400 uppercase tracking-wider">
                    PENGATURAN SISTEM
                </div>
                <div class="sidebar-section-divider hidden h-px bg-slate-200 my-2"></div>
                <div class="mt-1 space-y-0.5">
                    <!-- Kelola Apel -->
                    <a href="{{ route('schedules.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('schedules*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Kelola Apel">
                        <i data-lucide="calendar-plus" class="w-4 h-4 shrink-0 {{ request()->routeIs('schedules*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Kelola Apel</span>
                    </a>

                    <!-- Integrasi E-Kuliah -->
                    <a href="{{ route('ekuliah.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('ekuliah*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Integrasi E-Kuliah">
                        <i data-lucide="refresh-cw" class="w-4 h-4 shrink-0 {{ request()->routeIs('ekuliah*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Integrasi E-Kuliah</span>
                    </a>

                    <!-- Audit Trail -->
                    <a href="{{ route('audit-logs.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('audit-logs*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Audit Trail">
                        <i data-lucide="shield" class="w-4 h-4 shrink-0 {{ request()->routeIs('audit-logs*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Audit Trail</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Section 5: AKUN -->
        <div>
            <div class="sidebar-section-header px-2.5 py-1 text-[9.5px] font-bold text-slate-400 uppercase tracking-wider">
                AKUN
            </div>
            <div class="sidebar-section-divider hidden h-px bg-slate-200 my-2"></div>
            <div class="mt-1 space-y-0.5">
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('profile*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Edit Profil">
                    <i data-lucide="user-cog" class="w-4 h-4 shrink-0 {{ request()->routeIs('profile*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span class="sidebar-label truncate">Edit Profil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="sidebar-nav-link relative w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] text-red-600 hover:bg-red-50 transition-colors text-left font-medium" title="Logout">
                        <i data-lucide="log-out" class="w-4 h-4 shrink-0 text-red-500"></i>
                        <span class="sidebar-label truncate">Logout</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Bottom Footer Status -->
    <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 bg-white sidebar-footer-content">
        <div class="flex items-center gap-1.5 min-w-0" title="Akun Aktif">
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0"></span>
            <span class="font-medium text-slate-600 sidebar-footer-text whitespace-nowrap">Akun Aktif</span>
        </div>
        <a href="{{ route('ekuliah.index') }}" title="Pengaturan Integrasi Sistem" class="text-slate-400 hover:text-slate-600 transition-colors p-1">
            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
        </a>
    </div>

</aside>

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/30 z-30 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>
