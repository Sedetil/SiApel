@php
    // Mock data 8 pengajuan persis seperti desain UI mockup
    $mockItems = [
        [
            'id' => 101,
            'name' => 'Yoga Saputra',
            'npm' => '24572001',
            'initials' => 'YS',
            'avatar_bg' => 'bg-blue-100 text-blue-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '20 Okt • 16.30',
            'date_range' => '21 Oktober 2026',
            'is_active' => true,
            'reason' => 'Demam tinggi dan istirahat dokter',
            'status' => 'menunggu',
            'attachment' => 'surat_keterangan_sakit_yoga.pdf',
        ],
        [
            'id' => 102,
            'name' => 'Dewi Lestari',
            'npm' => '24572002',
            'initials' => 'DL',
            'avatar_bg' => 'bg-purple-100 text-purple-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '20 Okt • 15.12',
            'date_range' => '22–23 Oktober 2026',
            'is_active' => false,
            'reason' => 'Pemeriksaan rawat jalan dan pemulihan pasca operasi kecil',
            'status' => 'menunggu',
            'attachment' => 'surat_dokter_dewi_lestari.pdf',
        ],
        [
            'id' => 103,
            'name' => 'Rina Kusuma',
            'npm' => '24572003',
            'initials' => 'RK',
            'avatar_bg' => 'bg-sky-100 text-sky-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '20 Okt • 14.05',
            'date_range' => '23 Oktober 2026',
            'is_active' => false,
            'reason' => 'Flu berat dan rekomendasi istirahat klinik kampus',
            'status' => 'menunggu',
            'attachment' => 'surat_klinik_rina.pdf',
        ],
        [
            'id' => 104,
            'name' => 'Maya Permata',
            'npm' => '24572004',
            'initials' => 'MP',
            'avatar_bg' => 'bg-indigo-100 text-indigo-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '20 Okt • 11.20',
            'date_range' => '26–28 Oktober 2026',
            'is_active' => false,
            'reason' => 'Perawatan medis opname rumah sakit',
            'status' => 'menunggu',
            'attachment' => 'surat_rawat_inap_maya.pdf',
        ],
        [
            'id' => 105,
            'name' => 'Dimas Pratama',
            'npm' => '24572005',
            'initials' => 'DP',
            'avatar_bg' => 'bg-emerald-100 text-emerald-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '19 Okt • 16.48',
            'date_range' => '26–27 Oktober 2026',
            'is_active' => false,
            'reason' => 'Tifus ringan observasi istirahat di rumah',
            'status' => 'menunggu',
            'attachment' => 'surat_dokter_dimas.pdf',
        ],
        [
            'id' => 106,
            'name' => 'Siti Rahma',
            'npm' => '24572006',
            'initials' => 'SR',
            'avatar_bg' => 'bg-blue-100 text-blue-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '19 Okt • 14.30',
            'date_range' => '27 Oktober 2026',
            'is_active' => false,
            'reason' => 'Migrain akut dan vertigo dalam penanganan medis',
            'status' => 'menunggu',
            'attachment' => 'surat_keterangan_siti.pdf',
        ],
        [
            'id' => 107,
            'name' => 'Rafi Maulana',
            'npm' => '24572007',
            'initials' => 'RM',
            'avatar_bg' => 'bg-teal-100 text-teal-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '19 Okt • 10.15',
            'date_range' => '29–30 Oktober 2026',
            'is_active' => false,
            'reason' => 'Cedera pergelangan kaki latihan fisik butuh istirahat',
            'status' => 'menunggu',
            'attachment' => 'surat_dokter_rafi.pdf',
        ],
        [
            'id' => 108,
            'name' => 'Intan Pertiwi',
            'npm' => '24572008',
            'initials' => 'IP',
            'avatar_bg' => 'bg-slate-100 text-slate-600',
            'program' => 'Teknologi rekayasa perangkat lunak',
            'type' => 'sakit',
            'type_label' => 'Sakit',
            'submit_time' => '19 Okt • 09.42',
            'date_range' => '30 Oktober 2026',
            'is_active' => false,
            'reason' => 'Gejala demam berdarah perlu masa istirahat',
            'status' => 'menunggu',
            'attachment' => 'surat_lab_intan.pdf',
        ],
    ];

    $hasRealRequests = isset($requests) && $requests->isNotEmpty();
    $currentStatus = $status ?? 'menunggu';
    $waitingCount = ($stats['menunggu'] ?? 0) > 0 ? $stats['menunggu'] : 8;
@endphp

<!-- 4. CARD UTAMA (KONTAINER DAFTAR PENGAJUAN) -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-4">
    
    <!-- Header Card: Judul & Bulan/Tahun -->
    <div class="flex items-center justify-between">
        <h2 class="text-[15px] font-bold text-slate-900 tracking-tight">Daftar pengajuan Izin Sakit</h2>
        <span class="text-[13px] text-slate-400 font-medium">Oktober 2026</span>
    </div>

    <!-- Navigasi Tabs: Menunggu (badge 8), Disetujui, Ditolak, Semua -->
    <div class="border-b border-slate-100 flex items-center gap-6 text-[13px] pt-1">
        
        <!-- Tab: Menunggu (Aktif Default) -->
        <a href="{{ route('permission-requests.index', ['status' => 'menunggu', 'type' => $type, 'search' => $search]) }}"
           class="flex items-center gap-1.5 pb-2.5 font-semibold transition-colors cursor-pointer border-b-2 {{ $currentStatus === 'menunggu' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <span>Menunggu</span>
            <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-full text-[11px] font-bold {{ $currentStatus === 'menunggu' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
                {{ $waitingCount }}
            </span>
        </a>

        <!-- Tab: Disetujui -->
        <a href="{{ route('permission-requests.index', ['status' => 'disetujui', 'type' => $type, 'search' => $search]) }}"
           class="flex items-center gap-1.5 pb-2.5 font-medium transition-colors cursor-pointer border-b-2 {{ $currentStatus === 'disetujui' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <span>Disetujui</span>
        </a>

        <!-- Tab: Ditolak -->
        <a href="{{ route('permission-requests.index', ['status' => 'ditolak', 'type' => $type, 'search' => $search]) }}"
           class="flex items-center gap-1.5 pb-2.5 font-medium transition-colors cursor-pointer border-b-2 {{ $currentStatus === 'ditolak' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <span>Ditolak</span>
        </a>

        <!-- Tab: Semua -->
        <a href="{{ route('permission-requests.index', ['status' => 'all', 'type' => $type, 'search' => $search]) }}"
           class="flex items-center gap-1.5 pb-2.5 font-medium transition-colors cursor-pointer border-b-2 {{ $currentStatus === 'all' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <span>Semua</span>
        </a>

    </div>

    <!-- Baris Pencarian & Filter Dropdowns -->
    <form method="GET" action="{{ route('permission-requests.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
        <input type="hidden" name="status" value="{{ $currentStatus }}">

        <!-- Input Pencarian Lebar -->
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                placeholder="Cari nama atau NPM Mahasiswa..." 
                class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200/90 rounded-xl text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors shadow-xs"
            >
        </div>

        @if(auth()->check() && !auth()->user()->isAdmin() && !empty(auth()->user()->department))
            <!-- Timdis: Tampilkan Lingkup Pembinaan Terkunci -->
            <div class="flex items-center gap-2">
                <div class="bg-slate-100/80 border border-slate-200 text-slate-600 text-[12.5px] rounded-xl px-3.5 py-2 font-medium flex items-center gap-2" title="{{ auth()->user()->study_program ?? auth()->user()->department }} ({{ auth()->user()->class_group ?? 'Semua Kelas' }})">
                    <i data-lucide="shield-check" class="w-4 h-4 text-[#2D56FF] shrink-0"></i>
                    <span class="truncate max-w-[220px]">{{ auth()->user()->study_program ?? auth()->user()->department }}</span>
                </div>

                @php
                    $assignedClasses = auth()->user()->assigned_classes;
                @endphp
                @if(count($assignedClasses) > 1)
                    <!-- Multi-Scope: Dropdown pilihan kelas yang dipegang -->
                    <div class="relative min-w-[130px]">
                        <select name="class_group" onchange="this.form.submit()" class="w-full appearance-none bg-blue-50/70 border border-blue-200 text-[#2D56FF] rounded-xl px-3 py-2 pr-7 text-[12px] font-bold focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-xs">
                            <option value="">Semua Kelas</option>
                            @foreach($assignedClasses as $ac)
                                <option value="{{ $ac }}" {{ request('class_group') === $ac ? 'selected' : '' }}>
                                    {{ $ac }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#2D56FF] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                @elseif(count($assignedClasses) === 1)
                    <span class="bg-blue-50 border border-blue-200 text-[#2D56FF] text-[11.5px] font-bold px-2.5 py-2 rounded-xl shrink-0">
                        {{ $assignedClasses[0] }}
                    </span>
                @endif
            </div>
        @else
            <!-- Admin: Dropdown Lengkap Semua Jurusan & Prodi -->
            <!-- Dropdown 1: Jurusan -->
            <div class="relative min-w-[150px]">
                <select name="department" onchange="this.form.submit()" class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-xs truncate">
                    <option value="">Semua Jurusan</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ ($department ?? '') === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            <!-- Dropdown 2: Program Studi -->
            <div class="relative min-w-[170px]">
                <select name="study_program" onchange="this.form.submit()" class="w-full appearance-none bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 pr-8 text-[13px] text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-xs truncate">
                    <option value="">Semua Program Studi</option>
                    @foreach($studyPrograms as $sp)
                        <option value="{{ $sp }}" {{ ($studyProgram ?? '') === $sp ? 'selected' : '' }}>
                            {{ $sp }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
        @endif

        @if(request()->hasAny(['search', 'study_program', 'department']))
            <a href="{{ route('permission-requests.index', ['status' => $currentStatus]) }}" class="text-xs text-[#2D56FF] hover:underline font-semibold flex items-center gap-1 shrink-0 px-1" title="Reset Pencarian">
                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                <span>Reset</span>
            </a>
        @endif
    </form>

    <!-- Baris Info & Urutan -->
    <div class="flex items-center justify-between text-[12px] pt-1 pb-1">
        <span class="text-slate-500 font-medium">
            {{ $waitingCount }} pengajuan menunggu
        </span>

        <div class="flex items-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer font-medium transition-colors select-none">
            <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-slate-500"></i>
            <span>Tanggal izin terdekat</span>
            <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
        </div>
    </div>

    <!-- 5. TABEL / DAFTAR PENGAJUAN -->
    <div class="border border-slate-100 rounded-xl overflow-hidden">
        
        <!-- Header Tabel -->
        <div class="bg-slate-50/60 border-b border-slate-100 px-4 py-2.5 text-[11px] font-medium text-slate-400 flex items-center select-none">
            <div class="w-[45%] sm:w-[48%]">Mahasiswa</div>
            <div class="w-[28%] sm:w-[26%]">Pengajuan</div>
            <div class="flex-1 flex items-center justify-between pr-3">
                <span class="flex items-center gap-1">
                    Tanggal Pengajuan
                    <i data-lucide="arrow-up" class="w-3 h-3 text-slate-400"></i>
                </span>
            </div>
        </div>

        <!-- Daftar Baris Pengajuan -->
        <div class="divide-y divide-slate-100/90">

            @if($requests->isNotEmpty())
                @foreach($requests as $index => $req)
                    @php
                        $participant = $req->participant;
                        $name = $participant?->name ?? 'Mahasiswa';
                        $nameParts = preg_split('/\s+/', trim($name));
                        $initials = strtoupper(substr($nameParts[0] ?? 'M', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                        if(strlen($initials) < 2) $initials = strtoupper(substr($name, 0, 2));
                        $avatarBgs = [
                            'bg-blue-100 text-blue-600',
                            'bg-purple-100 text-purple-600',
                            'bg-sky-100 text-sky-600',
                            'bg-indigo-100 text-indigo-600',
                            'bg-emerald-100 text-emerald-600',
                            'bg-teal-100 text-teal-600'
                        ];
                        $avatarClass = $avatarBgs[$index % count($avatarBgs)];
                        $prodi = $participant?->study_program ?? 'Teknologi Rekayasa Perangkat Lunak';
                        $dateFormatted = $req->created_at ? $req->created_at->translatedFormat('d F Y') : '21 Oktober 2026';
                        $submitTime = $req->created_at ? $req->created_at->format('d M • H.i') : '20 Okt • 16.30';

                        // Data seragam untuk modal detail
                        $itemPayload = [
                            'id' => $req->id,
                            'name' => $name,
                            'npm' => $participant?->identifier ?? '22753000',
                            'program' => $prodi,
                            'class' => $participant?->class_group ?? 'TRPL 1B',
                            'avatar_bg' => $avatarClass,
                            'initials' => $initials,
                            'type' => $req->type,
                            'type_label' => ucfirst($req->type),
                            'reason' => $req->reason,
                            'date_range' => $dateFormatted,
                            'submit_time' => $submitTime,
                            'status' => $req->status,
                            'attachment' => $req->attachment_path ?? 'surat_keterangan.pdf',
                            'notes' => $req->notes,
                        ];
                    @endphp

                    <div 
                        onclick="openDetailModal({{ json_encode($itemPayload) }})"
                        class="px-4 py-3.5 flex items-center transition-colors cursor-pointer group border-l-4 border-transparent hover:bg-slate-50/70 hover:border-blue-600"
                    >
                        <!-- Kolom Mahasiswa: Avatar, Nama, Prodi -->
                        <div class="w-[45%] sm:w-[48%] flex items-center gap-3 min-w-0 pr-2">
                            <div class="w-9 h-9 rounded-full {{ $avatarClass }} font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-[13.5px] font-bold truncate text-slate-900 group-hover:text-blue-600 transition-colors">
                                    {{ $name }}
                                </div>
                                <div class="text-[11.5px] text-slate-400 font-normal truncate">
                                    {{ $prodi }}
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Pengajuan: Jenis & Waktu Input -->
                        <div class="w-[28%] sm:w-[26%] min-w-0 pr-2">
                            <div class="text-[13px] font-semibold text-slate-800 capitalize">
                                {{ $req->type }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-normal">
                                {{ $submitTime }}
                            </div>
                        </div>

                        <!-- Kolom Tanggal Pengajuan & Chevron Right -->
                        <div class="flex-1 flex items-center justify-between min-w-0 pr-1">
                            <span class="text-[12.5px] text-slate-700 font-medium truncate">
                                {{ $dateFormatted }}
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4 shrink-0 text-slate-300 group-hover:text-slate-500 transition-transform group-hover:translate-x-0.5"></i>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-10 text-center text-slate-400 text-xs">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                        <i data-lucide="inbox" class="w-5 h-5"></i>
                    </div>
                    <p class="font-bold text-slate-700 text-sm">Tidak Ada Pengajuan</p>
                    <p class="mt-1">Tidak ditemukan data pengajuan izin atau sakit untuk status ini pada mahasiswa binaan Anda.</p>
                </div>
            @endif

        </div>

    </div>

    <!-- Footer Tabel: Info Jumlah & Panduan Interaksi -->
    <div class="space-y-3 pt-1">
        <div class="text-[12px] text-slate-400 font-normal">
            Menampilkan {{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }} dari {{ number_format($requests->total()) }} pengajuan
        </div>

        @if($requests->hasPages())
            <div class="pt-2">
                {{ $requests->links('pagination::tailwind') }}
            </div>
        @endif

        <div class="bg-slate-50 border border-slate-100/90 rounded-xl p-3 px-3.5 flex items-center gap-2.5 text-slate-500 text-[12px]">
            <i data-lucide="info" class="w-4 h-4 text-slate-400 shrink-0"></i>
            <span>Pilih pengajuan untuk meninjau detail dan memberikan keputusan.</span>
        </div>
    </div>

</div>
