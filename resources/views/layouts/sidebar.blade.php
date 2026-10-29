<!-- SIDEBAR NAVIGATION -->
<aside id="app-sidebar" class="w-64 bg-white border-r border-slate-200/90 flex flex-col justify-between fixed top-0 bottom-0 left-0 z-40 transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 select-none">
    
    <!-- Top Area: Logo, Profile, and Nav Links -->
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">
        
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-1 pt-1 group" id="sidebar-logo" title="SiApel POLINELA">
            <img src="{{ asset('static/img/logo.png') }}" alt="Logo POLINELA" class="w-10 h-10 object-contain shrink-0 group-hover:scale-105 transition-transform">
            <div class="flex flex-col leading-tight sidebar-label overflow-hidden">
                <span class="font-extrabold text-[20px] tracking-tight text-slate-900 whitespace-nowrap">SiApel</span>
                <span class="text-[11px] font-medium text-slate-400 whitespace-nowrap">Sistem Absensi Apel Polinela</span>
            </div>
        </a>

        <!-- Divider 1 (antara Logo dan Profile) -->
        <div class="sidebar-section-divider border-t border-slate-200/90 my-2"></div>

        <!-- User Profile Card (Rounded pill/box with letter avatar and chevrons) -->
        <a href="{{ route('profile.edit') }}" class="rounded-xl p-2.5 bg-[#EEF2FF] border border-[#E0E7FF] flex items-center justify-between hover:bg-[#E5EDFF] transition-colors group sidebar-profile-card sidebar-user-card" title="Profil: {{ auth()->user()->name }}">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-white border border-[#E0E7FF] text-[#1E293B] font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'B', 0, 1)) }}
                </div>
                <div class="min-w-0 sidebar-label sidebar-profile-info overflow-hidden leading-tight">
                    <div class="font-bold text-[12px] text-[#1E293B] truncate group-hover:text-primary transition-colors" title="{{ auth()->user()->name }}">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-[11px] text-[#94A3B8] font-normal capitalize truncate">
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Timdis' }}
                    </div>
                </div>
            </div>
            <i data-lucide="chevrons-up-down" class="w-4 h-4 text-[#94A3B8] shrink-0 ml-1 group-hover:text-slate-600 sidebar-label"></i>
        </a>

        @if(!auth()->user()->isAdmin())
            @php
                $assignedClasses = auth()->user()->assigned_classes;
                $classCount = count($assignedClasses);
                $cohort = auth()->user()->cohort_year;
                $dept = auth()->user()->department;
                $prodi = auth()->user()->study_program;
            @endphp
            <!-- KARTU INFO WILAYAH BINAAN TIMDIS (COMPACT PILL + POPOVER HOVER) -->
            <div class="sidebar-label relative group/binaan">
                <div class="bg-slate-50 hover:bg-blue-50/50 border border-slate-200/90 hover:border-blue-300 rounded-xl p-2.5 transition-colors cursor-pointer flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-6 h-6 rounded-lg bg-blue-100/70 text-blue-600 flex items-center justify-center shrink-0">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        </div>
                        <div class="min-w-0 leading-tight">
                            <div class="text-[11.5px] font-bold text-slate-800 truncate">
                                @if($classCount > 0)
                                    {{ $classCount }} Kelas Binaan
                                @elseif($prodi)
                                    {{ $prodi }}
                                @elseif($dept)
                                    {{ $dept }}
                                @else
                                    Seluruh Kampus
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400 truncate mt-0.5">
                                {{ $prodi ?? $dept ?? 'Koordinator Apel' }}
                            </div>
                        </div>
                    </div>
                    <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400 group-hover/binaan:text-blue-600 shrink-0 transition-colors"></i>
                </div>

                <!-- POPOVER TOOLTIP SAAT HOVER / DETAIL RINCIAN -->
                <div class="absolute left-0 right-0 top-full mt-1.5 z-50 hidden group-hover/binaan:block bg-white border border-slate-200 rounded-xl shadow-lg p-3 text-xs space-y-2.5 animate-scaleUp">
                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-[10.5px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-3 h-3 text-blue-600"></i>
                            Rincian Pembinaan
                        </span>
                        @if($cohort)
                            <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                Angk. {{ $cohort }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1 text-[11px]">
                        @if($dept)
                            <div class="flex items-start gap-1">
                                <span class="text-slate-400 shrink-0 w-14">Jurusan:</span>
                                <span class="font-semibold text-slate-800">{{ $dept }}</span>
                            </div>
                        @endif
                        @if($prodi)
                            <div class="flex items-start gap-1">
                                <span class="text-slate-400 shrink-0 w-14">Prodi:</span>
                                <span class="font-semibold text-slate-800">{{ $prodi }}</span>
                            </div>
                        @endif
                    </div>

                    @if($classCount > 0)
                        <div class="pt-1.5 border-t border-slate-100">
                            <div class="text-[10px] font-medium text-slate-400 mb-1">
                                Daftar Kelas ({{ $classCount }}):
                            </div>
                            <div class="flex flex-wrap gap-1 max-h-[90px] overflow-y-auto">
                                @foreach($assignedClasses as $cls)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $cls }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if(!auth()->user()->isAdmin())
            <!-- ============================================== -->
            <!-- KHUSUS TIMDIS (SESUAI GAMBAR REFERENSI USER) -->
            <!-- ============================================== -->

                <!-- Divider 2 (antara Profile dan MENU UTAMA) -->
                <div class="sidebar-section-divider border-t border-slate-200/90 my-2"></div>

                <!-- Section: MENU UTAMA -->
                <div>
                    <div class="sidebar-section-header px-2 text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider mb-2">
                        MENU UTAMA
                    </div>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('timdis.dashboard') ? 'bg-white border border-[#2D56FF]/30 text-[#2D56FF] font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50' }}" title="Dashboard">
                            <i data-lucide="layout-grid" class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') || request()->routeIs('timdis.dashboard') ? 'text-[#2D56FF]' : 'text-[#94A3B8]' }}"></i>
                            <span class="sidebar-label truncate">Dashboard</span>
                        </a>

                        <!-- Mahasiswa Saya -->
                        <a href="{{ route('timdis.participants') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-colors {{ request()->routeIs('timdis.participants*') ? 'bg-white border border-[#2D56FF]/30 text-[#2D56FF] font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50' }}" title="Mahasiswa Saya">
                            <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0 {{ request()->routeIs('timdis.participants*') ? 'text-[#2D56FF]' : 'text-[#94A3B8]' }}"></i>
                            <span class="sidebar-label truncate">Mahasiswa Saya</span>
                        </a>
                    </div>
                </div>

                <!-- Divider 3 (antara MENU UTAMA dan MONITORING) -->
                <div class="sidebar-section-divider border-t border-slate-200/90 my-2"></div>

                <!-- Section: MONITORING -->
                <div>
                    <div class="sidebar-section-header px-2 text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider mb-2">
                        MONITORING
                    </div>
                    <div class="space-y-1">
                        <!-- Informasi Absensi (Active Highlighted pill with calendar icon) -->
                        <a href="{{ route('attendance.history') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-all {{ request()->routeIs('attendance.*') || request()->routeIs('timdis.attendance.*') ? 'bg-white border border-[#2D56FF]/30 text-[#2D56FF] font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50' }}" title="Informasi Absensi">
                            <i data-lucide="calendar" class="w-4 h-4 shrink-0 {{ request()->routeIs('attendance.*') || request()->routeIs('timdis.attendance.*') ? 'text-[#2D56FF]' : 'text-[#94A3B8]' }}"></i>
                            <span class="sidebar-label truncate">Informasi Absensi</span>
                        </a>
                    </div>
                </div>

                <!-- Divider 4 (antara MONITORING dan VERIFIKASI & APPROVAL) -->
                <div class="sidebar-section-divider border-t border-slate-200/90 my-2"></div>

                <!-- Section: VERIFIKASI & APPROVAL -->
                <div>
                    <div class="sidebar-section-header px-2 text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider mb-2">
                        VERIFIKASI & APPROVAL
                    </div>
                    <div class="space-y-1">
                        <!-- Izin & Sakit -->
                        <a href="{{ route('permission-requests.index') }}" class="sidebar-nav-link relative flex items-center justify-between px-3 py-2.5 rounded-xl text-[13px] transition-colors {{ request()->routeIs('permission-requests*') || request()->routeIs('timdis.permission-requests*') ? 'bg-white border border-[#2D56FF]/30 text-[#2D56FF] font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50' }}" title="Izin & Sakit">
                            <div class="flex items-center gap-3 min-w-0">
                                <i data-lucide="clipboard-check" class="w-4 h-4 shrink-0 {{ request()->routeIs('permission-requests*') || request()->routeIs('timdis.permission-requests*') ? 'text-[#2D56FF]' : 'text-[#94A3B8]' }}"></i>
                                <span class="sidebar-label truncate">Izin & Sakit</span>
                            </div>
                            @php
                                $pendingPermsCount = (Schema::hasTable('permission_requests') || Schema::hasTable('pengajuan_izin'))
                                    ? \App\Models\PermissionRequest::forUser(auth()->user())->where('status', 'menunggu')->count()
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

                <!-- Divider 5 (antara VERIFIKASI dan AKUN) -->
                <div class="sidebar-section-divider border-t border-slate-200/90 my-2"></div>

                <!-- Section: AKUN -->
                <div>
                    <div class="sidebar-section-header px-2 text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider mb-2">
                        AKUN
                    </div>
                    <div class="space-y-1">
                        <!-- Edit Profil -->
                        <a href="{{ route('profile.edit') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition-colors {{ request()->routeIs('profile*') || request()->routeIs('timdis.profile*') ? 'bg-white border border-[#2D56FF]/30 text-[#2D56FF] font-bold shadow-xs' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50' }}" title="Edit Profil">
                            <i data-lucide="user-cog" class="w-4 h-4 shrink-0 {{ request()->routeIs('profile*') || request()->routeIs('timdis.profile*') ? 'text-[#2D56FF]' : 'text-[#94A3B8]' }}"></i>
                            <span class="sidebar-label truncate">Edit Profil</span>
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="sidebar-nav-link relative w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] text-[#EF4444] hover:text-[#DC2626] hover:bg-red-50 transition-colors text-left font-medium" title="Logout">
                                <i data-lucide="log-out" class="w-4 h-4 shrink-0 text-[#EF4444]"></i>
                                <span class="sidebar-label truncate font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>

        @else
            <!-- ============================================== -->
            <!-- KHUSUS ADMINISTRATOR UTAMA -->
            <!-- ============================================== -->

            <!-- Section 1: MENU UTAMA -->
            <div>
                <div class="sidebar-section-header px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    MENU UTAMA
                </div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Dashboard</span>
                    </a>
                    <a href="{{ route('attendance.history') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('attendance.history*') || request()->routeIs('admin.attendance.history*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="history" class="w-4 h-4 shrink-0 {{ request()->routeIs('attendance.history*') || request()->routeIs('admin.attendance.history*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Riwayat Absensi</span>
                    </a>
                </div>
            </div>

            <!-- Section 2: VERIFIKASI & APPROVAL -->
            <div>
                <div class="sidebar-section-header px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    VERIFIKASI & APPROVAL
                </div>
                <div class="space-y-1">
                    <a href="{{ route('permission-requests.index') }}" class="sidebar-nav-link relative flex items-center justify-between px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('permission-requests*') || request()->routeIs('admin.permission-requests*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <i data-lucide="clipboard-check" class="w-4 h-4 shrink-0 {{ request()->routeIs('permission-requests*') || request()->routeIs('admin.permission-requests*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                            <span class="sidebar-label truncate">Izin & Sakit</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section 3: USER MANAGEMENT -->
            <div>
                <div class="sidebar-section-header px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    USER MANAGEMENT
                </div>
                <div class="space-y-1">
                    <a href="{{ route('participants.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('participants*') || request()->routeIs('admin.participants*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0 {{ request()->routeIs('participants*') || request()->routeIs('admin.participants*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Mahasiswa</span>
                    </a>
                    <a href="{{ route('admin.officers.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('officers*') || request()->routeIs('admin.officers*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="users" class="w-4 h-4 shrink-0 {{ request()->routeIs('officers*') || request()->routeIs('admin.officers*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Timdis</span>
                    </a>
                </div>
            </div>

            <!-- Section 4: PENGATURAN SISTEM -->
            <div>
                <div class="sidebar-section-header px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    PENGATURAN SISTEM
                </div>
                <div class="space-y-1">
                    <a href="{{ route('schedules.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('schedules*') || request()->routeIs('admin.schedules*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="calendar-plus" class="w-4 h-4 shrink-0 {{ request()->routeIs('schedules*') || request()->routeIs('admin.schedules*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Kelola Apel</span>
                    </a>
                    <a href="{{ route('admin.academic-structures.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('academic-structures*') || request()->routeIs('admin.academic-structures*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}" title="Struktur Jurusan & Kelas">
                        <i data-lucide="network" class="w-4 h-4 shrink-0 {{ request()->routeIs('academic-structures*') || request()->routeIs('admin.academic-structures*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Struktur & Kelas</span>
                    </a>
                    <a href="{{ route('ekuliah.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('ekuliah*') || request()->routeIs('admin.ekuliah*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="refresh-cw" class="w-4 h-4 shrink-0 {{ request()->routeIs('ekuliah*') || request()->routeIs('admin.ekuliah*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Integrasi E-Kuliah</span>
                    </a>
                    <a href="{{ route('audit-logs.index') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('audit-logs*') || request()->routeIs('admin.audit-logs*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="shield" class="w-4 h-4 shrink-0 {{ request()->routeIs('audit-logs*') || request()->routeIs('admin.audit-logs*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Audit Trail</span>
                    </a>
                </div>
            </div>

            <!-- Section 5: AKUN -->
            <div>
                <div class="sidebar-section-header px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    AKUN
                </div>
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="sidebar-nav-link relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] transition-colors {{ request()->routeIs('profile*') || request()->routeIs('admin.profile*') ? 'bg-blue-50 text-blue-600 font-semibold border border-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="user-cog" class="w-4 h-4 shrink-0 {{ request()->routeIs('profile*') || request()->routeIs('admin.profile*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                        <span class="sidebar-label truncate">Edit Profil</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="sidebar-nav-link relative w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] text-red-500 hover:text-red-600 hover:bg-red-50 transition-colors text-left font-medium">
                            <i data-lucide="log-out" class="w-4 h-4 shrink-0 text-red-500"></i>
                            <span class="sidebar-label truncate">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>

    <!-- Bottom Footer Status & Sidebar Collapse Toggle -->
    <div class="px-3 py-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 bg-white sidebar-footer-content">
        <div class="flex items-center gap-2 min-w-0 sidebar-footer-status" title="Akun Aktif">
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0"></span>
            <span class="font-medium text-slate-600 sidebar-footer-text whitespace-nowrap">Akun Aktif</span>
        </div>
        <button 
            type="button"
            id="sidebar-slider-toggle"
            class="text-slate-400 hover:text-primary hover:bg-slate-100 p-1.5 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer flex items-center justify-center shrink-0"
            aria-label="Perkecil atau Perbesar Sidebar"
            title="Perkecil / Perbesar Sidebar"
        >
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
        </button>
    </div>

</aside>

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/30 z-30 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>
