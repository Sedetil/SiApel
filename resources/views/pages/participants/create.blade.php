@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Tambah Peserta Apel
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Daftarkan mahasiswa baru dan hubungkan ke struktur akademik kampus.
            </p>
        </div>
        <a href="{{ route('admin.participants.index') }}" class="text-[13px] font-medium text-text-secondary hover:text-text-primary">
            &larr; Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-surface border border-border rounded-lg p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.participants.store') }}" class="space-y-5">
            @csrf

            <!-- NPM Mahasiswa -->
            <div>
                <label for="identifier" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    NPM Mahasiswa <span class="text-error">*</span>
                </label>
                <input 
                    type="text" 
                    id="identifier" 
                    name="identifier" 
                    value="{{ old('identifier') }}" 
                    required 
                    placeholder="Contoh: 22753045"
                    class="w-full font-mono font-bold text-[14px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                >
                @error('identifier')
                    <p class="text-[12px] text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    Nama Lengkap <span class="text-error">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: Rian Hidayat"
                    class="w-full text-[14px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                >
                @error('name')
                    <p class="text-[12px] text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hierarki Akademik (Cascading Dropdowns dari Master) -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[11.5px] font-bold text-slate-700 uppercase tracking-wider">
                        Struktur Akademik Mahasiswa
                    </span>
                    <a href="{{ route('admin.academic-structures.index') }}" target="_blank" class="text-[11px] text-primary hover:underline">
                        + Kelola Master Struktur
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="department" class="block text-[11px] font-medium text-slate-600 mb-1.5">
                            Jurusan
                        </label>
                        <select 
                            id="department" 
                            name="department" 
                            onchange="updateProdiOptions()" 
                            class="w-full text-[13px] px-3.5 py-2.5 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}" {{ old('department') === $dept->name ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="study_program" class="block text-[11px] font-medium text-slate-600 mb-1.5">
                            Program Studi
                        </label>
                        <select 
                            id="study_program" 
                            name="study_program" 
                            onchange="updateClassOptions()" 
                            class="w-full text-[13px] px-3.5 py-2.5 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="class_group" class="block text-[11px] font-medium text-slate-600 mb-1.5">
                            Kelas Mahasiswa
                        </label>
                        <select 
                            id="class_group" 
                            name="class_group" 
                            onchange="updateCohortFromClass()" 
                            class="w-full text-[13px] px-3.5 py-2.5 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                            <option value="">-- Pilih Kelas --</option>
                        </select>
                    </div>

                    <div>
                        <label for="cohort_year" class="block text-[11px] font-medium text-slate-600 mb-1.5">
                            Tahun Angkatan
                        </label>
                        <input 
                            type="text" 
                            id="cohort_year" 
                            name="cohort_year" 
                            value="{{ old('cohort_year') }}" 
                            placeholder="Terisi otomatis atau ketik manual" 
                            class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>
                </div>
            </div>

            <!-- Gender & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Jenis Kelamin
                    </label>
                    <select id="gender" name="gender" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>

                <div>
                    <label for="phone" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        No. Telepon / WhatsApp
                    </label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}" 
                        placeholder="Contoh: 08123456789"
                        class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>
            </div>

            <!-- Initial RFID Card UID (Optional) -->
            <div class="pt-2 border-t border-border">
                <label for="initial_card_uid" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    UID Kartu RFID Perdana (Opsional)
                </label>
                <input 
                    type="text" 
                    id="initial_card_uid" 
                    name="initial_card_uid" 
                    value="{{ old('initial_card_uid') }}" 
                    placeholder="Contoh: E2000035 (Dapat dipetakan nanti jika belum ada)"
                    class="w-full font-mono font-bold text-[14px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                <p class="text-[11px] text-text-secondary mt-1">
                    Jika kartu sudah tersedia, Anda dapat langsung menempelkannya ke reader di sini.
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('admin.participants.index') }}" class="px-4 py-2.5 border border-border text-text-secondary hover:bg-background rounded-md text-[13px] font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                    Simpan Peserta
                </button>
            </div>

        </form>
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

    // Initialize initial values if old() is present
    document.addEventListener('DOMContentLoaded', function() {
        const initialDept = "{{ old('department') }}";
        const initialProdi = "{{ old('study_program') }}";
        const initialClass = "{{ old('class_group') }}";

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
