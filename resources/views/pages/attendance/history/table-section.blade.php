<!-- ATTENDANCE RECORDS TABLE SECTION (CARD PUTIH, LEBAR PENUH) -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-4">
    
    <!-- Header Tabel -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="w-10 h-10 rounded-xl bg-[#EEF2FF] border border-[#E0E7FF] text-[#2D56FF] flex items-center justify-center shrink-0">
            <i data-lucide="clock" class="w-5 h-5 text-[#2D56FF]"></i>
        </div>
        <div>
            <h3 class="text-[15px] font-bold text-slate-900 tracking-tight leading-tight">
                Absensi Terkini
            </h3>
            <p class="text-[12px] text-slate-400 mt-0.5">
                Log kehadiran mahasiswa yang baru saja terekam mesin barcode / tap kartu
            </p>
        </div>
    </div>

    @php
        // Palette warna inisial avatar (RH, DP, MR, SN, FK dsb)
        $avatarColors = [
            ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
            ['bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
            ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
            ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
            ['bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
            ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
            ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
        ];
    @endphp

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[12.5px]">
            <thead>
                <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                    <th class="py-3 px-4">
                        MAHASISWA
                    </th>
                    <th class="py-3 px-4">
                        JURUSAN
                    </th>
                    <th class="py-3 px-4">
                        PROGRAM STUDI
                    </th>
                    <th class="py-3 px-4">
                        JAM MASUK
                    </th>
                    <th class="py-3 px-4">
                        STATUS KEHADIRAN
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100/80">
                @forelse($records as $index => $rec)
                    @php
                        $pName = $rec->participant?->name ?? 'Nama Mahasiswa';
                        $pNpm = $rec->participant?->identifier ?? 'NPM-'.str_pad($rec->participant_id ?? 1, 8, '0', STR_PAD_LEFT);
                        $pDept = $rec->participant?->department ?? 'Teknologi Informasi';
                        $pStudy = $rec->participant?->study_program ?? 'D4 Teknologi Rekayasa Perangkat Lunak';

                        // Inisial avatar
                        $pParts = preg_split('/\s+/', trim($pName));
                        $initials = '';
                        if (count($pParts) >= 2) {
                            $initials = strtoupper(substr($pParts[0], 0, 1) . substr($pParts[1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($pName, 0, 2));
                        }
                        $colorScheme = $avatarColors[$index % count($avatarColors)];

                        // Jam masuk & Status Kehadiran
                        $isRecorded = !in_array($rec->status, ['alpa', 'tidak_hadir']) && $rec->check_in_time;
                        $timeStr = $isRecorded ? $rec->check_in_time->format('H:i') : '—';

                        $statusBadge = match($rec->status) {
                            'hadir' => [
                                'label' => 'Hadir Tepat',
                                'badge' => 'bg-[#ecfdf5] text-[#16a34a] border border-emerald-200/80',
                                'dot' => 'bg-[#16a34a]',
                            ],
                            'terlambat' => [
                                'label' => 'Terlambat',
                                'badge' => 'bg-[#fef2f2] text-[#ef4444] border border-rose-200/80',
                                'dot' => 'bg-[#ef4444]',
                            ],
                            'sakit' => [
                                'label' => 'Sakit',
                                'badge' => 'bg-[#fffbeb] text-[#d97706] border border-amber-200/80',
                                'dot' => 'bg-[#f59e0b]',
                            ],
                            'izin' => [
                                'label' => 'Izin',
                                'badge' => 'bg-[#eff6ff] text-[#2563eb] border border-blue-200/80',
                                'dot' => 'bg-[#2563eb]',
                            ],
                            'alpa', 'tidak_hadir' => [
                                'label' => 'Tidak Hadir',
                                'badge' => 'bg-slate-100 text-slate-700 border border-slate-200/80',
                                'dot' => 'bg-slate-800',
                            ],
                            default => [
                                'label' => 'Hadir Tepat',
                                'badge' => 'bg-[#ecfdf5] text-[#16a34a] border border-emerald-200/80',
                                'dot' => 'bg-[#16a34a]',
                            ]
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Kolom 1: Mahasiswa (Avatar Inisial Bulat + Nama Huruf Tebal + NPM) -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $colorScheme['bg'] }} {{ $colorScheme['text'] }} font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div class="font-bold text-[13px] text-slate-900 leading-tight uppercase">
                                        {{ $pName }}
                                    </div>
                                    <div class="text-[11.5px] font-mono text-slate-400 mt-0.5">
                                        {{ $pNpm }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Kolom 2: Jurusan (Badge Abu-abu Rounded) -->
                        <td class="py-3.5 px-4">
                            <span class="inline-block bg-[#F1F5F9] text-slate-700 text-[11.5px] font-medium px-2.5 py-1 rounded-lg">
                                {{ $pDept }}
                            </span>
                        </td>

                        <!-- Kolom 3: Program Studi -->
                        <td class="py-3.5 px-4">
                            <span class="text-slate-700 text-[12.5px] font-medium">
                                {{ $pStudy }}
                            </span>
                        </td>

                        <!-- Kolom 4: Jam Masuk -->
                        <td class="py-3.5 px-4">
                            <span class="font-bold {{ $isRecorded ? 'text-slate-900 font-mono text-[13px]' : 'text-slate-300 text-[14px]' }}">
                                {{ $timeStr }}
                            </span>
                        </td>

                        <!-- Kolom 5: Status Kehadiran (Pill Badge) -->
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold {{ $statusBadge['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                <span>{{ $statusBadge['label'] }}</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <i data-lucide="clipboard-x" class="w-6 h-6"></i>
                            </div>
                            <div class="font-bold text-slate-700 text-sm">Tidak Ada Data Presensi</div>
                            <div class="text-xs text-slate-400 mt-1">Tidak ditemukan catatan kehadiran yang sesuai dengan kriteria filter.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
