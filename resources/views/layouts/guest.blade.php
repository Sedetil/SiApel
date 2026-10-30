<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'SIABSEN POLINELA') }}</title>

    <!-- Google Fonts: DM Sans, IBM Plex Mono, Inter (StyleGuide.md) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=IBM+Plex+Mono:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @stack('styles')
</head>
<body class="min-h-full bg-background font-sans text-text-primary antialiased flex flex-col selection:bg-primary selection:text-white">

    <!-- Main Content for Guest / Auth Pages -->
    <main class="flex-1 flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Guest Minimal Footer -->
    <footer class="py-4 text-center text-[12px] text-text-secondary border-t border-border/50">
        <p>SIABSEN POLINELA &bull; Politeknik Negeri Lampung &copy; {{ date('Y') }}</p>
        <p class="text-[11px] mt-0.5 text-text-secondary/80">Sistem Absensi Apel Berbasis RFID &bull; Versi 1.0 (Tema Terang)</p>
    </footer>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
