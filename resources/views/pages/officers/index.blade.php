@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Kelola Akun Timdis
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Manajemen akun dosen pembina/timdis apel dan penetapan wilayah binaan (Jurusan, Prodi, Kelas, Angkatan).
            </p>
        </div>

        <button 
            type="button" 
            onclick="openCreateModal()" 
            class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-4 py-2.5 rounded-lg text-[13px] font-semibold transition-colors shadow-sm self-start sm:self-auto"
        >
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Timdis</span>
        </button>
    </div>

    <!-- Officers Table Card -->
    <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
        @if($officers->isEmpty())
            <div class="p-12 text-center text-text-secondary text-[14px]">
                <p class="font-bold text-text-primary">Belum Ada Akun Timdis</p>
                <p class="text-[12px] mt-0.5">Silakan buat akun timdis baru untuk memantau presensi apel.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-background border-b border-border text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        <tr>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4">Wilayah Binaan (Scope)</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Terakhir Masuk</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach($officers as $officer)
                            <tr class="hover:bg-background/40 transition-colors">
                                <td class="py-3 px-4 font-bold text-text-primary">
                                    {{ $officer->name }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-primary">
                                    {{ $officer->username }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($officer->department || $officer->study_program || $officer->class_group)
                                        <div class="space-y-0.5 text-[11.5px]">
                                            @if($officer->department)
                                                <div class="font-bold text-slate-800">{{ $officer->department }}</div>
                                            @endif
                                            <div class="text-slate-500">
                                                {{ $officer->study_program ?? 'Semua Prodi' }}
                                                @if(!empty($officer->assigned_classes))
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($officer->assigned_classes as $cls)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                                {{ $cls }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @elseif($officer->class_group)
                                                    &bull; <strong class="text-blue-600">{{ $officer->class_group }}</strong>
                                                @endif
                                                @if($officer->cohort_year)
                                                    <span class="text-slate-400">({{ $officer->cohort_year }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Seluruh Kampus (Koordinator)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-text-secondary">
                                    {{ $officer->email }}
                                </td>
                                <td class="py-3 px-4 text-text-secondary text-[12px]">
                                    {{ $officer->last_login_at ? $officer->last_login_at->format('d/m/Y H:i') : 'Belum pernah' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($officer->isActive())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[11px] font-bold bg-success/10 text-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[11px] font-bold bg-error/10 text-error">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit Trigger -->
                                        <button 
                                            type="button" 
                                            onclick="openEditModal('{{ $officer->id }}', '{{ addslashes($officer->name) }}', '{{ $officer->username }}', '{{ $officer->email }}', '{{ addslashes($officer->department ?? '') }}', '{{ addslashes($officer->study_program ?? '') }}', '{{ addslashes($officer->class_group ?? '') }}', '{{ $officer->cohort_year ?? '' }}')"
                                            class="p-1.5 rounded-md hover:bg-background text-text-secondary hover:text-text-primary transition-colors"
                                            title="Ubah Akun"
                                        >
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ route('officers.toggle-status', $officer) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif akun Timdis {{ $officer->name }}?')">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-md hover:bg-background {{ $officer->isActive() ? 'text-text-secondary hover:text-warning' : 'text-success' }} transition-colors" title="{{ $officer->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <i data-lucide="power" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                        <!-- Delete Akun -->
                                        <form method="POST" action="{{ route('officers.destroy', $officer) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Timdis {{ $officer->name }} secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-md hover:bg-red-50 text-text-secondary hover:text-red-600 transition-colors" title="Hapus Akun Timdis">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-border bg-surface">
                {{ $officers->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Timdis -->
<div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-lg w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Tambah Akun Timdis</h3>
            <button type="button" onclick="closeCreateModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('officers.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="create_name" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">
                        Nama Lengkap <span class="text-error">*</span>
                    </label>
                    <input type="text" id="create_name" name="name" required placeholder="Budi Santoso, M.T" class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label for="create_username" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">
                        Username Login <span class="text-error">*</span>
                    </label>
                    <input type="text" id="create_username" name="username" required placeholder="timdis_trpl_1b" class="w-full font-mono text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="create_email" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">
                        Email Resmi <span class="text-error">*</span>
                    </label>
                    <input type="email" id="create_email" name="email" required placeholder="budi@polinela.ac.id" class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label for="create_password" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">
                        Kata Sandi <span class="text-error">*</span>
                    </label>
                    <input type="password" id="create_password" name="password" required minlength="6" placeholder="Min. 6 karakter" class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- Scope Wilayah Binaan (Cascading Dropdowns) -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">
                        Penugasan Wilayah Binaan Apel
                    </span>
                    <span class="text-[10.5px] text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        Mendukung Lintas Prodi & Jurusan
                    </span>
                </div>

                <!-- Bagian 1: Pilih Cepat Jurusan & Prodi Utama (Opsional untuk Koordinator) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_department" class="block text-[11px] font-medium text-slate-600 mb-1">Filter Jurusan</label>
                        <select id="create_department" name="department" onchange="updateProdiOptions('create')" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                            <option value="">-- Semua Jurusan Kampus --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->name }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create_study_program" class="block text-[11px] font-medium text-slate-600 mb-1">Filter Program Studi</label>
                        <select id="create_study_program" name="study_program" onchange="updateClassOptions('create')" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                            <option value="">-- Semua Program Studi --</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian 2: Badge Selector Kelas Lintas Jurusan/Prodi -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-medium text-slate-600">
                            Kelas Binaan yang Dipegang <span class="text-blue-600 font-bold" id="create_selected_count">(0 kelas terpilih)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllVisibleClasses('create')" class="text-[11px] font-semibold text-blue-600 hover:underline">
                                Pilih Semua
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" onclick="clearAllClasses('create')" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 hover:underline">
                                Hapus Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Tempat Checkbox Badges Kelas -->
                    <div id="create_classes_container" class="max-h-[160px] overflow-y-auto p-2 bg-white border border-slate-200 rounded-lg flex flex-wrap gap-1.5 items-center content-start">
                        <!-- Dimuat via Javascript -->
                    </div>

                    <!-- Hidden Inputs Sinkronisasi untuk Form Submit -->
                    <div id="create_hidden_classes_inputs"></div>

                    <!-- Input Tersembunyi Fallback / Tahun Angkatan -->
                    <div class="pt-1">
                        <label for="create_cohort_year" class="block text-[11px] font-medium text-slate-600 mb-1">Tahun Angkatan Mahasiswa (Opsional)</label>
                        <input type="text" id="create_cohort_year" name="cohort_year" placeholder="Contoh: 2024, 2025 (otomatis sinkron)" class="w-full font-mono text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Akun Timdis</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Timdis -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-lg w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Ubah Data Timdis</h3>
            <button type="button" onclick="closeEditModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit_name" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" id="edit_name" name="name" required class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label for="edit_username" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">Username Login</label>
                    <input type="text" id="edit_username" name="username" required class="w-full font-mono text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit_email" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">Email Resmi</label>
                    <input type="email" id="edit_email" name="email" required class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label for="edit_password" class="block text-[11.5px] font-bold text-text-primary uppercase tracking-wider mb-1">Ganti Sandi (Opsional)</label>
                    <input type="password" id="edit_password" name="password" minlength="6" placeholder="Kosongkan jika tetap" class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- Scope Wilayah Binaan Edit -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">
                        Penugasan Wilayah Binaan Apel
                    </span>
                    <span class="text-[10.5px] text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        Mendukung Lintas Prodi & Jurusan
                    </span>
                </div>

                <!-- Bagian 1: Filter Cepat Jurusan & Prodi Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_department" class="block text-[11px] font-medium text-slate-600 mb-1">Filter Jurusan</label>
                        <select id="edit_department" name="department" onchange="updateProdiOptions('edit')" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                            <option value="">-- Semua Jurusan Kampus --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->name }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_study_program" class="block text-[11px] font-medium text-slate-600 mb-1">Filter Program Studi</label>
                        <select id="edit_study_program" name="study_program" onchange="updateClassOptions('edit')" class="w-full text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                            <option value="">-- Semua Program Studi --</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian 2: Badge Selector Kelas Lintas Jurusan/Prodi -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-medium text-slate-600">
                            Kelas Binaan yang Dipegang <span class="text-blue-600 font-bold" id="edit_selected_count">(0 kelas terpilih)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllVisibleClasses('edit')" class="text-[11px] font-semibold text-blue-600 hover:underline">
                                Pilih Semua
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" onclick="clearAllClasses('edit')" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 hover:underline">
                                Hapus Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Tempat Checkbox Badges Kelas -->
                    <div id="edit_classes_container" class="max-h-[160px] overflow-y-auto p-2 bg-white border border-slate-200 rounded-lg flex flex-wrap gap-1.5 items-center content-start">
                        <!-- Dimuat via Javascript -->
                    </div>

                    <!-- Hidden Inputs Sinkronisasi untuk Form Submit -->
                    <div id="edit_hidden_classes_inputs"></div>

                    <!-- Input Tahun Angkatan -->
                    <div class="pt-1">
                        <label for="edit_cohort_year" class="block text-[11px] font-medium text-slate-600 mb-1">Tahun Angkatan Mahasiswa (Opsional)</label>
                        <input type="text" id="edit_cohort_year" name="cohort_year" placeholder="Contoh: 2024, 2025 (otomatis sinkron)" class="w-full font-mono text-[12.5px] px-3 py-2 bg-white border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const academicStructureData = @json($departments);

    // Kumpulan kelas terpilih (Key-Value Map per Modal)
    // Structure: { 'TRPL 1A': { name: 'TRPL 1A', cohort: '2025', prodi: 'TRPL', dept: 'JTI' } }
    const selectedClassesMap = {
        create: new Map(),
        edit: new Map()
    };

    function getDepartmentByName(deptName) {
        return academicStructureData.find(d => d.name === deptName);
    }

    // Ambil seluruh kelas dari database kampus POLINELA (dengan filter opsional)
    function getAllAvailableClasses(deptName = '', prodiName = '') {
        const result = [];
        academicStructureData.forEach(dept => {
            if (deptName && dept.name !== deptName) return;
            if (dept.study_programs) {
                dept.study_programs.forEach(prodi => {
                    if (prodiName && prodi.name !== prodiName) return;
                    if (prodi.classes) {
                        prodi.classes.forEach(cls => {
                            result.push({
                                name: cls.name,
                                cohort: cls.cohort_year || '',
                                prodi: prodi.name,
                                dept: dept.name
                            });
                        });
                    }
                });
            }
        });
        return result;
    }

    function updateProdiOptions(prefix) {
        const deptName = document.getElementById(prefix + '_department').value;
        const prodiSelect = document.getElementById(prefix + '_study_program');
        prodiSelect.innerHTML = '<option value="">-- Semua Program Studi --</option>';

        if (deptName) {
            const dept = getDepartmentByName(deptName);
            if (dept && dept.study_programs) {
                dept.study_programs.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.textContent = p.name + ' (' + p.degree_level + ')';
                    prodiSelect.appendChild(opt);
                });
            }
        } else {
            // Tampilkan semua prodi dari seluruh jurusan
            academicStructureData.forEach(dept => {
                if (dept.study_programs) {
                    dept.study_programs.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.name;
                        opt.textContent = p.name + ' (' + dept.name + ')';
                        prodiSelect.appendChild(opt);
                    });
                }
            });
        }
        renderClassBadges(prefix);
    }

    function updateClassOptions(prefix) {
        renderClassBadges(prefix);
    }

    function renderClassBadges(prefix) {
        const deptName = document.getElementById(prefix + '_department').value;
        const prodiName = document.getElementById(prefix + '_study_program').value;
        const container = document.getElementById(prefix + '_classes_container');
        container.innerHTML = '';

        const classes = getAllAvailableClasses(deptName, prodiName);

        if (classes.length === 0) {
            container.innerHTML = '<span class="text-[11.5px] text-slate-400 italic">Tidak ada kelas yang sesuai dengan filter di atas.</span>';
            return;
        }

        classes.forEach(c => {
            const isChecked = selectedClassesMap[prefix].has(c.name);
            const label = document.createElement('label');
            label.className = `cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11.5px] font-semibold transition-all select-none ${
                isChecked 
                    ? 'bg-blue-50 border-blue-500 text-blue-700 shadow-xs' 
                    : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'
            }`;
            
            label.innerHTML = `
                <input type="checkbox" value="${c.name}" data-cohort="${c.cohort}" ${isChecked ? 'checked' : ''} class="w-3.5 h-3.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer" onchange="toggleClassBadge(this, '${prefix}', '${c.name}', '${c.cohort}', '${c.prodi}', '${c.dept}')">
                <span>${c.name}</span>
                <span class="text-[10px] ${isChecked ? 'text-blue-500 font-normal' : 'text-slate-400 font-normal'}">(${c.prodi})</span>
            `;
            container.appendChild(label);
        });

        updateHeaderSummary(prefix);
    }

    function toggleClassBadge(checkbox, prefix, name, cohort, prodi, dept) {
        if (checkbox.checked) {
            selectedClassesMap[prefix].set(name, { name, cohort, prodi, dept });
        } else {
            selectedClassesMap[prefix].delete(name);
        }

        const label = checkbox.closest('label');
        if (checkbox.checked) {
            label.className = 'cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11.5px] font-semibold transition-all select-none bg-blue-50 border-blue-500 text-blue-700 shadow-xs';
        } else {
            label.className = 'cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11.5px] font-semibold transition-all select-none bg-white border-slate-200 text-slate-700 hover:border-slate-300';
        }

        updateHeaderSummary(prefix);
    }

    function selectAllVisibleClasses(prefix) {
        const container = document.getElementById(prefix + '_classes_container');
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');

        checkboxes.forEach(cb => {
            if (!cb.checked) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change'));
            }
        });
    }

    function clearAllClasses(prefix) {
        selectedClassesMap[prefix].clear();
        const container = document.getElementById(prefix + '_classes_container');
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            const label = cb.closest('label');
            if (label) {
                label.className = 'cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11.5px] font-semibold transition-all select-none bg-white border-slate-200 text-slate-700 hover:border-slate-300';
            }
        });
        updateHeaderSummary(prefix);
    }

    function updateHeaderSummary(prefix) {
        const count = selectedClassesMap[prefix].size;
        const countEl = document.getElementById(prefix + '_selected_count');
        if (countEl) {
            countEl.textContent = `(${count} kelas terpilih)`;
        }

        // Sinkronisasi Hidden Inputs Form Submission
        const hiddenContainer = document.getElementById(prefix + '_hidden_classes_inputs');
        if (hiddenContainer) {
            hiddenContainer.innerHTML = '';
            selectedClassesMap[prefix].forEach((item, className) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'class_groups[]';
                input.value = className;
                hiddenContainer.appendChild(input);
            });
        }

        // Kumpulkan angkatan otomatis
        const cohorts = new Set();
        selectedClassesMap[prefix].forEach(item => {
            if (item.cohort) cohorts.add(item.cohort);
        });

        const cohortInput = document.getElementById(prefix + '_cohort_year');
        if (cohortInput) {
            cohortInput.value = Array.from(cohorts).sort().join(', ');
        }
    }

    function openCreateModal() {
        selectedClassesMap.create.clear();
        document.getElementById('create_department').value = '';
        updateProdiOptions('create');
        renderClassBadges('create');
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(id, name, username, email, department, studyProgram, classGroup, cohortYear) {
        document.getElementById('editForm').action = '/officers/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_password').value = '';

        // Reset dan inisialisasi Map kelas terpilih
        selectedClassesMap.edit.clear();
        if (classGroup) {
            const arr = classGroup.split(',').map(s => s.trim()).filter(Boolean);
            arr.forEach(cName => {
                selectedClassesMap.edit.set(cName, { name: cName, cohort: '' });
            });
        }

        document.getElementById('edit_department').value = department || '';
        updateProdiOptions('edit');
        document.getElementById('edit_study_program').value = studyProgram || '';
        
        renderClassBadges('edit');
        document.getElementById('edit_cohort_year').value = cohortYear;

        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
