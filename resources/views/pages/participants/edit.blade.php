@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Ubah Data Peserta
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Perbarui biodata peserta, penempatan akademik, dan kelola kartu RFID yang terhubung.
            </p>
        </div>
        <a href="{{ route('participants.index') }}" class="text-[13px] font-medium text-text-secondary hover:text-text-primary">
            &larr; Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Form: Participant Details (7 cols) -->
        <div class="lg:col-span-7 bg-surface border border-border rounded-lg p-6 shadow-sm">
            <h2 class="text-[16px] font-bold text-text-primary mb-4 pb-2 border-b border-border">Informasi Biodata</h2>

            <form method="POST" action="{{ route('admin.participants.update', $participant) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="identifier" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                        NPM / NIM Mahasiswa <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="identifier" 
                        name="identifier" 
                        value="{{ old('identifier', $participant->identifier) }}" 
                        required 
                        class="w-full font-mono font-bold text-[14px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                    @error('identifier')
                        <p class="text-[12px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $participant->name) }}" 
                        required 
                        class="w-full text-[14px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <!-- Hierarki Akademik (Cascading Dropdowns) -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">
                        Struktur Akademik
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="department" class="block text-[11px] font-medium text-slate-600 mb-1">Jurusan</label>
                            <select id="department" name="department" onchange="updateProdiOptions()" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ old('department', $participant->department) === $dept->name ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="study_program" class="block text-[11px] font-medium text-slate-600 mb-1">Program Studi</label>
                            <select id="study_program" name="study_program" onchange="updateClassOptions()" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                                <option value="">-- Pilih Program Studi --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="class_group" class="block text-[11px] font-medium text-slate-600 mb-1">Kelas Mahasiswa</label>
                            <select id="class_group" name="class_group" onchange="updateCohortFromClass()" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                                <option value="">-- Pilih Kelas --</option>
                            </select>
                        </div>

                        <div>
                            <label for="cohort_year" class="block text-[11px] font-medium text-slate-600 mb-1">Tahun Angkatan</label>
                            <input type="text" id="cohort_year" name="cohort_year" value="{{ old('cohort_year', $participant->cohort_year) }}" placeholder="Tahun angkatan" class="w-full font-mono text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="gender" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                            Jenis Kelamin
                        </label>
                        <select id="gender" name="gender" class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                            <option value="">Pilih</option>
                            <option value="L" {{ old('gender', $participant->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $participant->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="phone" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                            Telepon / WA
                        </label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $participant->phone) }}" 
                            class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>
                </div>

                <div>
                    <label for="is_active" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                        Status Peserta
                    </label>
                    <select id="is_active" name="is_active" class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                        <option value="1" {{ old('is_active', $participant->is_active) ? 'selected' : '' }}>Aktif (Dapat Mengikuti Apel)</option>
                        <option value="0" {{ !old('is_active', $participant->is_active) ? 'selected' : '' }}>Nonaktif (Blokir Presensi)</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-border flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: RFID Card Management (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Current Active Card Card -->
            <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                <h2 class="text-[16px] font-bold text-text-primary mb-3 pb-2 border-b border-border">Kartu RFID Terhubung</h2>

                @if($participant->activeRfidCard)
                    <div class="bg-blue-50/60 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-primary">Kartu Aktif</span>
                                <div class="font-mono text-[18px] font-bold text-primary mt-1">
                                    {{ $participant->activeRfidCard->card_uid }}
                                </div>
                                <div class="text-[12px] text-text-secondary mt-1">
                                    Dipetakan: {{ $participant->activeRfidCard->issued_at ? $participant->activeRfidCard->issued_at->format('d/m/Y H:i') : '-' }}
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-success/10 text-success">
                                Aktif
                            </span>
                        </div>

                        <!-- Unbind Action -->
                        <form method="POST" action="{{ route('admin.participants.deactivate-rfid', $participant) }}" class="mt-4 pt-3 border-t border-blue-200/60" onsubmit="return confirm('Lepas tautan kartu RFID dari peserta ini?')">
                            @csrf
                            <button type="submit" class="w-full py-1.5 px-3 bg-white border border-border hover:bg-red-50 hover:text-error hover:border-error/40 text-text-secondary text-[12px] font-medium rounded transition-colors text-center">
                                Lepas Tautan Kartu
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-6 text-text-secondary border border-dashed border-border rounded-lg mb-4">
                        <i data-lucide="credit-card" class="w-8 h-8 mx-auto text-text-secondary/50 mb-2"></i>
                        <p class="text-[13px] font-bold text-text-primary">Belum Ada Kartu Aktif</p>
                        <p class="text-[11px] text-text-secondary mt-0.5">Petakan UID kartu baru di bawah ini.</p>
                    </div>
                @endif

                <!-- Map New Card Form -->
                <form method="POST" action="{{ route('admin.participants.map-rfid', $participant) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="card_uid" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1">
                            {{ $participant->activeRfidCard ? 'Ganti dengan Kartu Baru' : 'Petakan Kartu RFID' }}
                        </label>
                        <input 
                            type="text" 
                            id="card_uid" 
                            name="card_uid" 
                            required 
                            placeholder="Tempel kartu atau ketik UID" 
                            class="w-full font-mono font-bold text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>
                    <div>
                        <input 
                            type="text" 
                            name="notes" 
                            placeholder="Catatan (contoh: Kartu pengganti hilang)" 
                            class="w-full text-[12px] px-3 py-1.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>
                    <button type="submit" class="w-full py-2 bg-text-primary hover:bg-black text-white text-[12px] font-medium rounded-md transition-colors shadow-xs">
                        {{ $participant->activeRfidCard ? 'Perbarui Kartu RFID' : 'Simpan Pemetaan Kartu' }}
                    </button>
                </form>
            </div>

            <!-- RFID History -->
            @if($participant->rfidCards->count() > 1 || (!$participant->activeRfidCard && $participant->rfidCards->isNotEmpty()))
                <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                    <h3 class="text-[13px] font-bold text-text-primary uppercase tracking-wider mb-2">Riwayat Kartu Sebelumnya</h3>
                    <div class="divide-y divide-border text-[12px]">
                        @foreach($participant->rfidCards as $card)
                            <div class="py-2 flex items-center justify-between">
                                <div>
                                    <span class="font-mono font-bold text-text-primary">{{ $card->card_uid }}</span>
                                    <div class="text-[10px] text-text-secondary">{{ $card->notes ?? 'Tidak ada catatan' }}</div>
                                </div>
                                <span class="text-[10px] font-bold {{ $card->isActive() ? 'text-success' : 'text-text-secondary' }}">
                                    {{ $card->isActive() ? 'Aktif Saat Ini' : 'Dinonaktifkan' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
    const academicStructureData = @json($departments);

    function getDepartmentByName(deptName) {
        return academicStructureData.find(d => d.name === deptName);
    }

    function updateProdiOptions(selectedProdi = '') {
        const deptName = document.getElementById('department').value;
        const prodiSelect = document.getElementById('study_program');
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';

        if (deptName) {
            const dept = getDepartmentByName(deptName);
            if (dept && dept.study_programs) {
                dept.study_programs.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.textContent = p.name + ' (' + p.degree_level + ')';
                    if (p.name === selectedProdi) opt.selected = true;
                    prodiSelect.appendChild(opt);
                });
            }
        }
        updateClassOptions();
    }

    function updateClassOptions(selectedClass = '') {
        const deptName = document.getElementById('department').value;
        const prodiName = document.getElementById('study_program').value;
        const classSelect = document.getElementById('class_group');
        classSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';

        if (deptName && prodiName) {
            const dept = getDepartmentByName(deptName);
            const prodi = dept?.study_programs?.find(p => p.name === prodiName);
            if (prodi && prodi.classes) {
                prodi.classes.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.dataset.cohort = c.cohort_year;
                    opt.textContent = c.name + ' - Angkatan ' + c.cohort_year;
                    if (c.name === selectedClass) opt.selected = true;
                    classSelect.appendChild(opt);
                });
            }
        }
    }

    function updateCohortFromClass() {
        const classSelect = document.getElementById('class_group');
        const cohortInput = document.getElementById('cohort_year');
        const selectedOpt = classSelect.options[classSelect.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.cohort) {
            cohortInput.value = selectedOpt.dataset.cohort;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialDept = "{{ old('department', $participant->department) }}";
        const initialProdi = "{{ old('study_program', $participant->study_program) }}";
        const initialClass = "{{ old('class_group', $participant->class_group) }}";

        if (initialDept) {
            updateProdiOptions(initialProdi);
            if (initialProdi) {
                updateClassOptions(initialClass);
            }
        }
    });
</script>
@endpush
@endsection
