<!-- ATTENDANCE RECORDS TABLE SECTION (CARD PUTIH, LEBAR PENUH) -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_1px_3px_rgba(15,23,42,0.03)] p-6 space-y-4">
    
    <!-- Header Tabel -->
    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
        <div class="w-9 h-9 rounded-xl bg-[#eff6ff] text-[#2563eb] flex items-center justify-center shrink-0">
            <i data-lucide="clock" class="w-4 h-4"></i>
        </div>
        <div>
            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">
                Absensi Terkini
            </h3>
            <p class="text-[11.5px] text-slate-400 mt-0.5">
                Log kehadiran mahasiswa yang baru saja terekam mesin barcode / tap kartu
            </p>
        </div>
    </div>

    @php
        $hasFilters = request()->hasAny(['search', 'status', 'date', 'study_program', 'class_group', 'schedule_id']);
        
        // 5 baris mock data berulang seperti di desain mockup
        $mockRows = [
            ['name' => 'SITI NURHALIZA', 'prodi' => 'D4 Teknologi rekayasa perangkat lunak', 'jurusan' => 'Teknologi informasi', 'time' => '07:00', 'initials' => 'SN'],
            ['name' => 'SITI NURHALIZA', 'prodi' => 'D4 Teknologi rekayasa perangkat lunak', 'jurusan' => 'Teknologi informasi', 'time' => '07:00', 'initials' => 'SN'],
            ['name' => 'SITI NURHALIZA', 'prodi' => 'D4 Teknologi rekayasa perangkat lunak', 'jurusan' => 'Teknologi informasi', 'time' => '07:00', 'initials' => 'SN'],
            ['name' => 'SITI NURHALIZA', 'prodi' => 'D4 Teknologi rekayasa perangkat lunak', 'jurusan' => 'Teknologi informasi', 'time' => '07:00', 'initials' => 'SN'],
            ['name' => 'SITI NURHALIZA', 'prodi' => 'D4 Teknologi rekayasa perangkat lunak', 'jurusan' => 'Teknologi informasi', 'time' => '07:00', 'initials' => 'SN'],
        ];
    @endphp

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="py-3 px-4 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                        MAHASISWA
                    </th>
                    <th class="py-3 px-4 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                        JURUSAN
                    </th>
                    <th class="py-3 px-4 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                        JAM MASUK
                    </th>
                    <th class="py-3 px-4 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                        STATUS KEHADIRAN
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100/80">
                @if($hasFilters)
                    {{-- Tampilan saat filter/pencarian dijalankan --}}
                    @forelse($records as $rec)
                        @php
                            $pName = $rec->participant?->name ?? 'Mahasiswa';
                            $pParts = explode(' ', trim($pName));
                            $pInitials = count($pParts) >= 2 
                                ? strtoupper(substr($pParts[0], 0, 1) . substr($pParts[1], 0, 1))
                                : strtoupper(substr($pName, 0, 2));
                            $pStudy = $rec->participant?->study_program ?? 'D4 Teknologi rekayasa perangkat lunak';
                            $pTime = $rec->check_in_time ? $rec->check_in_time->format('H:i') : '07:00';
                            
                            $statusLabel = match($rec->status) {
                                'hadir' => 'Hadir Tepat Waktu',
                                'terlambat' => 'Terlambat',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpa' => 'Tidak Hadir',
                                default => 'Hadir Tepat Waktu'
                            };

                            $badgeClass = match($rec->status) {
                                'hadir' => 'bg-[#ecfdf5] text-[#16a34a] border border-emerald-100/60',
                                'terlambat' => 'bg-[#fef2f2] text-[#ef4444] border border-red-100/60',
                                'sakit' => 'bg-[#fffbeb] text-[#d97706] border border-amber-100/60',
                                'izin' => 'bg-[#eff6ff] text-[#2563eb] border border-blue-100/60',
                                'alpa' => 'bg-slate-100 text-slate-700 border border-slate-200/60',
                                default => 'bg-[#ecfdf5] text-[#16a34a] border border-emerald-100/60'
                            };

                            $dotClass = match($rec->status) {
                                'hadir' => 'bg-[#16a34a]',
                                'terlambat' => 'bg-[#ef4444]',
                                'sakit' => 'bg-[#f59e0b]',
                                'izin' => 'bg-[#2563eb]',
                                'alpa' => 'bg-slate-700',
                                default => 'bg-[#16a34a]'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Kolom 1: Mahasiswa -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#f3e8ff] text-[#9333ea] font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $pInitials }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[12.5px] text-slate-800 leading-tight uppercase">
                                            {{ $pName }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $pStudy }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 2: Jurusan -->
                            <td class="py-3.5 px-4">
                                <span class="inline-block bg-slate-100 text-slate-600 text-[11px] font-medium px-3 py-1 rounded-md">
                                    {{ $pStudy }}
                                </span>
                            </td>

                            <!-- Kolom 3: Jam Masuk -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 text-[13px]">
                                    {{ $pTime }}
                                </span>
                            </td>

                            <!-- Kolom 4: Status Kehadiran -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    <span>{{ $statusLabel }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <i data-lucide="clipboard-x" class="w-6 h-6"></i>
                                </div>
                                <div class="font-bold text-slate-700 text-sm">Tidak Ada Data Presensi</div>
                                <div class="text-xs text-slate-400 mt-1">Tidak ditemukan catatan kehadiran yang sesuai dengan kriteria filter.</div>
                            </td>
                        </tr>
                    @endforelse
                @else
                    {{-- Tampilan Default (5 Baris Mock Data SITI NURHALIZA sesuai Desain UI Mockup) --}}
                    @foreach($mockRows as $row)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Kolom 1: Mahasiswa -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#f3e8ff] text-[#9333ea] font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $row['initials'] }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[12.5px] text-slate-800 leading-tight uppercase">
                                            {{ $row['name'] }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $row['prodi'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 2: Jurusan -->
                            <td class="py-3.5 px-4">
                                <span class="inline-block bg-slate-100 text-slate-600 text-[11px] font-medium px-3 py-1 rounded-md">
                                    {{ $row['jurusan'] }}
                                </span>
                            </td>

                            <!-- Kolom 3: Jam Masuk -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 text-[13px]">
                                    {{ $row['time'] }}
                                </span>
                            </td>

                            <!-- Kolom 4: Status Kehadiran -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-[#ecfdf5] text-[#16a34a] border border-emerald-100/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                    <span>Hadir Tepat Waktu</span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

</div>
