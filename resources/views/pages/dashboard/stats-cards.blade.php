<!-- TOTAL MAHASISWA SAYA (Blue Hero Card) -->
<div class="rounded-2xl p-6 text-white flex flex-col justify-between relative overflow-hidden shadow-[0_4px_16px_rgba(27,55,185,0.2)] bg-[#1053D4] min-h-[175px]">
    
    <div class="z-10 flex items-start justify-between gap-4">
        <div class="min-w-0">
            <span class="text-[11px] font-bold text-blue-100 uppercase tracking-wider block">
                {{ $stats['is_timdis'] ? 'TOTAL MAHASISWA SAYA' : 'TOTAL MAHASISWA POLINELA' }}
            </span>
            <div class="text-[36px] sm:text-[40px] font-extrabold tracking-tight mt-1 font-sans leading-none">
                {{ number_format($stats['total_participants']) }}
            </div>
        </div>

        <!-- Illustration inside blue card -->
        <div class="shrink-0 pointer-events-none -mt-2 -mr-1">
            <img src="{{ asset('static/img/icon1.png') }}" alt="Ilustrasi Mahasiswa" class="h-24 sm:h-28 object-contain drop-shadow">
        </div>
    </div>

    <div class="z-10 mt-3">
        <span class="text-[10px] sm:text-[10.5px] font-semibold text-blue-100 tracking-wider uppercase block opacity-95">
            AKTIF TERDAFTAR DALAM SISTEM
        </span>
    </div>

</div>
