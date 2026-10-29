<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'SIABSEN POLINELA') }}</title>

    <!-- Google Fonts: Inter & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=IBM+Plex+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets (Tailwind CSS v4 & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-body: #f4f7fb;
            --bg-white: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --primary-blue: #1d63dc;
            --border-color: #e2e8f0;
            --card-radius: 12px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
        }

        /* Sidebar Transition & Collapsed Icon-Only Mode */
        #app-sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #main-wrapper {
            transition: padding-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (min-width: 1024px) {
            html.sidebar-collapsed #app-sidebar,
            #app-sidebar.sidebar-collapsed {
                width: 72px !important;
            }

            html.sidebar-collapsed #main-wrapper,
            #main-wrapper.sidebar-collapsed {
                padding-left: 72px !important;
            }

            html.sidebar-collapsed #app-sidebar > div.flex-1,
            #app-sidebar.sidebar-collapsed > div.flex-1 {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-label,
            #app-sidebar.sidebar-collapsed .sidebar-label {
                display: none !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-section-header,
            #app-sidebar.sidebar-collapsed .sidebar-section-header {
                display: none !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-section-divider,
            #app-sidebar.sidebar-collapsed .sidebar-section-divider {
                display: block !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-nav-link,
            #app-sidebar.sidebar-collapsed .sidebar-nav-link {
                width: 44px !important;
                height: 44px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 12px !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-user-card,
            #app-sidebar.sidebar-collapsed .sidebar-user-card {
                width: 44px !important;
                height: 44px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 12px !important;
                overflow: hidden !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-user-card > div,
            #app-sidebar.sidebar-collapsed .sidebar-user-card > div {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-user-card .w-7,
            #app-sidebar.sidebar-collapsed .sidebar-user-card .w-7 {
                width: 28px !important;
                height: 28px !important;
                margin: 0 auto !important;
                flex-shrink: 0 !important;
            }

            html.sidebar-collapsed #app-sidebar #sidebar-logo,
            #app-sidebar.sidebar-collapsed #sidebar-logo {
                width: 44px !important;
                height: 44px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-badge,
            #app-sidebar.sidebar-collapsed .sidebar-badge {
                position: absolute;
                top: 2px;
                right: 3px;
                padding: 1px 4px;
                font-size: 9px;
                line-height: 1;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-footer-text,
            #app-sidebar.sidebar-collapsed .sidebar-footer-text {
                display: none !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-footer-content,
            #app-sidebar.sidebar-collapsed .sidebar-footer-content {
                justify-content: center !important;
                padding: 0.75rem 0.5rem !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-footer-content > div:first-child {
                display: none !important;
            }

            html.sidebar-collapsed #app-sidebar .sidebar-footer-content a {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 36px !important;
                height: 36px !important;
                border-radius: 8px !important;
            }
        }

        /* Modern Sleek Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
            border: 1px solid transparent;
            background-clip: content-box;
            transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        #app-sidebar *::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        #app-sidebar *::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 9999px;
        }

        #app-sidebar *::-webkit-scrollbar-thumb:hover {
            background-color: #cbd5e1;
        }
    </style>

    <!-- Instant Pre-Hydration to prevent layout shifts -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 1024) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    @stack('styles')
</head>
<body class="h-full bg-[#f4f7fb] text-slate-800 antialiased flex selection:bg-blue-600 selection:text-white">

    <!-- SIDEBAR NAVIGATION COMPONENT -->
    @include('layouts.sidebar')

    <!-- MAIN WRAPPER -->
    <div id="main-wrapper" class="lg:pl-60 flex-1 flex flex-col min-h-screen w-full min-w-0 transition-all duration-200">
        
        <!-- TOPBAR NAVIGATION COMPONENT -->
        @include('layouts.topbar')

        <!-- MAIN CONTAINER -->
        <main class="flex-1 px-4 sm:px-7 py-6 max-w-[1360px] w-full mx-auto">
            
            <!-- Feedback Flash Messages -->
            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs shadow-sm" role="alert">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="font-bold">Berhasil:</strong> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-5 flex items-start gap-3 p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs shadow-sm" role="alert">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500 mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="font-bold">Perhatian:</strong> {{ session('warning') }}
                    </div>
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="mb-5 flex items-start gap-3 p-3.5 rounded-xl bg-red-50 border border-red-200/80 text-red-800 text-xs shadow-sm" role="alert">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="font-bold">Terjadi Kesalahan:</strong>
                        @if (session('error'))
                            <span>{{ session('error') }}</span>
                        @endif
                        @if ($errors->any())
                            <ul class="list-disc list-inside mt-1 space-y-0.5 text-slate-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Content Slot / Section -->
            {{ $slot ?? '' }}
            @yield('content')

        </main>

    </div>

    <!-- Sidebar Interaction Script & Lucide Initializer -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const mainWrapper = document.getElementById('main-wrapper');

            function toggleSidebar() {
                if (!sidebar) return;

                if (window.innerWidth < 1024) {
                    // Mobile Drawer behavior
                    const isHidden = sidebar.classList.contains('-translate-x-full');
                    if (isHidden) {
                        sidebar.classList.remove('-translate-x-full');
                        if (overlay) overlay.classList.remove('hidden');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        if (overlay) overlay.classList.add('hidden');
                    }
                } else {
                    // Desktop Icon-Only Collapse behavior
                    const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                    sidebar.classList.toggle('sidebar-collapsed', isCollapsed);
                    if (mainWrapper) {
                        mainWrapper.classList.toggle('sidebar-collapsed', isCollapsed);
                    }
                    try {
                        localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
                    } catch (e) {}
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    if (window.innerWidth < 1024 && sidebar) {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    }
                });
            }

            // Sync initial state on desktop
            if (window.innerWidth >= 1024 && document.documentElement.classList.contains('sidebar-collapsed')) {
                sidebar?.classList.add('sidebar-collapsed');
                mainWrapper?.classList.add('sidebar-collapsed');
            }

            if (window.createIcons && window.lucideIcons) {
                window.createIcons({ icons: window.lucideIcons });
            } else if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
