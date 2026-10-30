<!-- HERO GREETING BANNER -->
<div class="rounded-xl px-7 py-5 flex flex-col md:flex-row md:items-center justify-between relative overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.03)] border border-blue-100/60" style="background: linear-gradient(90deg, #e8f0fe 0%, #eef2ff 55%, #e8f0fe 100%); min-height: 112px;">
    
    <!-- Greeting Text -->
    <div class="z-10 py-1">
        <h3 class="text-base font-semibold text-slate-800 tracking-tight">Good Morning,</h3>
        <h2 class="text-[22px] font-bold text-[#1d63dc] tracking-tight leading-tight mt-0.5">
            {{ auth()->user()->name ?? 'I Komang Artayasa' }}!!
        </h2>
        <p class="text-xs text-slate-500 mt-1 font-medium">Stay Sistem Absensi Polinela</p>
    </div>

    <!-- Decorative SVG Cloud & Collaboration Illustration -->
    <div class="hidden sm:flex items-center pr-2 lg:pr-6 shrink-0 mt-3 md:mt-0 pointer-events-none">
        <img src="{{ asset('static/img/icon2.png') }}" alt="Ilustrasi Kolaborasi" class="h-20 sm:h-24 md:h-24 max-w-[280px] lg:max-w-[340px] object-contain">
    </div>

</div>
