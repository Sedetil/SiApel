<!-- HERO GREETING BANNER -->
<div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] flex flex-col md:flex-row md:items-center justify-between relative overflow-hidden gap-6">
    
    <!-- Greeting Text -->
    <div class="z-10 py-1 max-w-sm sm:max-w-md shrink-0">
        <h3 class="text-base sm:text-[18px] font-bold text-slate-900 tracking-tight leading-tight">
            Selamat Datang,
        </h3>
        <h2 class="text-[24px] sm:text-[28px] font-bold text-[#2D56FF] tracking-tight leading-tight mt-1 line-clamp-2" title="{{ auth()->user()->name }}">
            {{ auth()->user()->name ?? 'Bintang Kesuma Mahardika' }}
        </h2>
        <p class="text-xs sm:text-[13px] text-slate-400 mt-2 font-normal">
            Di Sistem Informasi Absensi Apel Polinela
        </p>

        @if(!auth()->user()->isAdmin())
            @php
                $assigned = auth()->user()->assigned_classes;
            @endphp
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold text-slate-500 flex items-center gap-1.5 uppercase tracking-wider">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-600"></i>
                    Binaan:
                </span>

                @if(auth()->user()->department)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ auth()->user()->department }}
                    </span>
                @endif

                @if(auth()->user()->study_program)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ auth()->user()->study_program }}
                    </span>
                @endif

                @php
                    $assignedCount = count($assigned);
                @endphp
                @if($assignedCount > 0)
                    <!-- Ringkas jika kelas banyak: Tampilkan 2 kelas pertama + badge +N lainnya -->
                    <div class="flex items-center gap-1">
                        @foreach(array_slice($assigned, 0, 2) as $cls)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#2D56FF] text-white shadow-2xs">
                                {{ $cls }}
                            </span>
                        @endforeach

                        @if($assignedCount > 2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-100 text-blue-800" title="{{ implode(', ', array_slice($assigned, 2)) }}">
                                +{{ $assignedCount - 2 }} lainnya
                            </span>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->cohort_year)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                        Angkatan {{ auth()->user()->cohort_year }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Decorative Illustration -->
    <div class="flex items-center justify-center md:justify-end shrink min-w-0 pointer-events-none pr-1">
        <img src="{{ asset('static/img/icon2.png') }}" alt="Ilustrasi Selamat Datang" class="h-28 sm:h-36 md:h-40 max-w-full object-contain">
    </div>

</div>
