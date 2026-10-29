<!-- FOOTER PAGINATION & INFO DATA -->
<div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 pb-6 select-none">

    <!-- Sisi Kiri: Menampilkan X–Y dari Z data & Dropdown Per Halaman -->
    <div class="flex items-center gap-3 text-xs text-slate-500">
        <span>
            Menampilkan {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} dari {{ number_format($records->total()) }} data
        </span>

        <div class="relative inline-block">
            <form method="GET" action="{{ route('attendance.history') }}" id="attendance-per-page-form">
                @foreach(request()->except(['per_page', 'page']) as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $arrV)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $arrV }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <select 
                    name="per_page" 
                    onchange="document.getElementById('attendance-per-page-form').submit()"
                    class="bg-white border border-slate-200 text-slate-700 text-xs rounded-xl px-3 py-1.5 pr-8 appearance-none cursor-pointer focus:outline-none focus:ring-1 focus:ring-primary font-medium hover:bg-slate-50 transition-colors shadow-2xs"
                >
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 / halaman</option>
                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 / halaman</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 / halaman</option>
                    <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 / halaman</option>
                </select>
            </form>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>
    </div>

    <!-- Sisi Kanan: Nomor Navigasi Halaman -->
    @if ($records->hasPages())
        <div class="flex items-center gap-1.5 overflow-x-auto">
            
            {{-- Previous Page Link --}}
            @if ($records->onFirstPage())
                <span class="w-9 h-9 rounded-lg border border-slate-200/60 flex items-center justify-center text-slate-300 bg-slate-50 cursor-not-allowed" aria-disabled="true">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </span>
            @else
                <a href="{{ $records->previousPageUrl() }}" rel="prev" class="w-9 h-9 rounded-lg border border-slate-200/90 flex items-center justify-center text-slate-600 bg-white hover:bg-slate-50 transition shadow-2xs cursor-pointer" aria-label="Halaman Sebelumnya">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($records->links()->elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-9 h-9 flex items-center justify-center text-xs font-bold text-slate-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $records->currentPage())
                            <span class="w-9 h-9 rounded-lg border border-slate-300 font-bold text-slate-900 bg-slate-100 flex items-center justify-center text-xs transition shadow-2xs cursor-default" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-9 h-9 rounded-lg border border-slate-200/90 font-semibold text-slate-700 bg-white hover:bg-slate-50 flex items-center justify-center text-xs transition shadow-2xs cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}" rel="next" class="w-9 h-9 rounded-lg border border-slate-200/90 flex items-center justify-center text-slate-600 bg-white hover:bg-slate-50 transition shadow-2xs cursor-pointer" aria-label="Halaman Berikutnya">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            @else
                <span class="w-9 h-9 rounded-lg border border-slate-200/60 flex items-center justify-center text-slate-300 bg-slate-50 cursor-not-allowed" aria-disabled="true">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </span>
            @endif

        </div>
    @endif

</div>
