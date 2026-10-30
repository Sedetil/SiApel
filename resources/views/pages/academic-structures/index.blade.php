@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                    Struktur Akademik & Kelas
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-sm text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                    Master Hierarki
                </span>
            </div>
            <p class="text-[13px] text-text-secondary mt-1">
                Kelola pembagian hierarki Jurusan, Program Studi, Kelas, dan Angkatan sebagai acuan seleksi data mahasiswa dan penugasan Timdis.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openDeptModal()" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-4 py-2.5 rounded-lg text-[13px] font-semibold transition-colors shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Jurusan</span>
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[12px] font-bold uppercase tracking-wider text-text-secondary">Total Jurusan</span>
                <i data-lucide="building" class="w-4 h-4 text-primary"></i>
            </div>
            <div class="text-[28px] font-mono font-bold text-text-primary">
                {{ $stats['total_departments'] }}
            </div>
            <p class="text-[11px] text-text-secondary mt-1">Fakultas / Jurusan Utama Kampus</p>
        </div>

        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[12px] font-bold uppercase tracking-wider text-text-secondary">Program Studi</span>
                <i data-lucide="graduation-cap" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div class="text-[28px] font-mono font-bold text-blue-600">
                {{ $stats['total_programs'] }}
            </div>
            <p class="text-[11px] text-text-secondary mt-1">Jenjang D3 & Sarjana Terapan (D4)</p>
        </div>

        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[12px] font-bold uppercase tracking-wider text-text-secondary">Rombongan Belajar / Kelas</span>
                <i data-lucide="users" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="text-[28px] font-mono font-bold text-emerald-600">
                {{ $stats['total_classes'] }}
            </div>
            <p class="text-[11px] text-text-secondary mt-1">Kelas Aktif Seluruh Angkatan</p>
        </div>
    </div>

    <!-- Hierarchical Tree Container -->
    <div class="space-y-4">
        @forelse($departments as $dept)
            <div class="bg-surface border border-border rounded-xl shadow-xs overflow-hidden transition-all">
                <!-- Department Bar Header -->
                <div class="p-4 sm:p-5 bg-slate-50/70 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shadow-2xs">
                            <i data-lucide="folder" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-[15px] font-bold text-slate-800">{{ $dept->name }}</h2>
                                @if($dept->code)
                                    <span class="px-2 py-0.5 rounded-sm text-[10px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $dept->code }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-[11.5px] text-slate-500 font-medium">
                                {{ $dept->studyPrograms->count() }} Program Studi terdaftar
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="openProdiModal('{{ $dept->id }}', '{{ addslashes($dept->name) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-[12px] font-semibold rounded-md shadow-2xs transition-colors">
                            <i data-lucide="plus" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Tambah Prodi</span>
                        </button>

                        <!-- Edit Department Button -->
                        <button type="button" onclick="openEditDeptModal('{{ $dept->id }}', '{{ addslashes($dept->name) }}', '{{ $dept->code ?? '' }}')" class="p-1.5 rounded-md hover:bg-slate-200/70 text-slate-500 hover:text-slate-800 transition-colors" title="Ubah Jurusan">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>

                        <form method="POST" action="{{ route('admin.academic-structures.departments.destroy', $dept) }}" onsubmit="return confirm('Hapus Jurusan {{ $dept->name }} beserta seluruh prodi dan kelas di dalamnya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-md hover:bg-red-50 text-slate-400 hover:text-red-600 transition-colors" title="Hapus Jurusan">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Study Programs Inside Department -->
                <div class="p-4 sm:p-5 space-y-4">
                    @forelse($dept->studyPrograms as $prodi)
                        <div class="border border-slate-200/90 rounded-lg p-4 bg-white hover:border-blue-200 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-3 border-b border-slate-100 gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <h3 class="font-bold text-[13.5px] text-slate-800">{{ $prodi->name }}</h3>
                                    <span class="px-2 py-0.5 rounded-sm text-[10px] font-bold bg-slate-100 text-slate-600">
                                        {{ $prodi->degree_level }}
                                    </span>
                                    @if($prodi->code)
                                        <span class="font-mono text-[11px] text-slate-400">({{ $prodi->code }})</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="openClassModal('{{ $prodi->id }}', '{{ addslashes($prodi->name) }}')" class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        <span>Kelas</span>
                                    </button>

                                    <!-- Edit Study Program Button -->
                                    <button type="button" onclick="openEditProdiModal('{{ $prodi->id }}', '{{ addslashes($prodi->name) }}', '{{ $prodi->degree_level }}', '{{ $prodi->code ?? '' }}')" class="p-1 text-slate-400 hover:text-slate-700 transition-colors" title="Ubah Prodi">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.academic-structures.study-programs.destroy', $prodi) }}" onsubmit="return confirm('Hapus Program Studi {{ $prodi->name }} beserta kelas di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-300 hover:text-red-500 transition-colors" title="Hapus Prodi">
                                            <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Classes Badge / Pills Container -->
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Daftar Kelas & Angkatan:</span>
                                @if($prodi->classes->isEmpty())
                                    <p class="text-[12px] text-slate-400 italic">Belum ada kelas terdaftar untuk program studi ini.</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($prodi->classes as $cls)
                                            <div class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[12px] font-medium text-slate-700 hover:bg-white hover:border-blue-300 transition-colors">
                                                <span class="font-bold text-slate-800">{{ $cls->name }}</span>
                                                <span class="text-[10.5px] font-mono px-1.5 py-0.5 rounded-sm bg-slate-200/70 text-slate-600">
                                                    {{ $cls->cohort_year }}
                                                </span>

                                                <!-- Edit Class Button -->
                                                <button type="button" onclick="openEditClassModal('{{ $cls->id }}', '{{ addslashes($cls->name) }}', '{{ $cls->cohort_year }}')" class="text-slate-400 hover:text-blue-600 transition-colors opacity-70 group-hover:opacity-100" title="Ubah Kelas">
                                                    <i data-lucide="edit-2" class="w-3 h-3"></i>
                                                </button>

                                                <!-- Delete Class Button -->
                                                <form method="POST" action="{{ route('admin.academic-structures.classes.destroy', $cls) }}" class="inline" onsubmit="return confirm('Hapus kelas {{ $cls->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-300 hover:text-red-500 opacity-60 group-hover:opacity-100 transition-opacity" title="Hapus Kelas">
                                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-[12px] text-slate-400 italic py-2">Belum ada program studi di jurusan ini. Klik "Tambah Prodi" untuk menambahkan.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-surface border border-border rounded-xl p-12 text-center text-text-secondary">
                <i data-lucide="folder-plus" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                <p class="font-bold text-text-primary text-[15px]">Belum Ada Struktur Akademik</p>
                <p class="text-[13px] mt-0.5">Mulai dengan menambahkan Jurusan pertama Anda.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- 1. Modal Tambah Jurusan -->
<div id="deptModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Tambah Jurusan Baru</h3>
            <button type="button" onclick="closeDeptModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.academic-structures.departments.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="dept_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Jurusan <span class="text-error">*</span></label>
                <input type="text" id="dept_name" name="name" required placeholder="Contoh: Teknologi Informasi" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="dept_code" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Kode Jurusan / Singkatan</label>
                <input type="text" id="dept_code" name="code" placeholder="Contoh: TI atau EKBIS" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeDeptModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Jurusan</button>
            </div>
        </form>
    </div>
</div>

<!-- 1b. Modal Ubah Jurusan -->
<div id="editDeptModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Ubah Data Jurusan</h3>
            <button type="button" onclick="closeEditDeptModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editDeptForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_dept_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Jurusan <span class="text-error">*</span></label>
                <input type="text" id="edit_dept_name" name="name" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="edit_dept_code" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Kode Jurusan / Singkatan</label>
                <input type="text" id="edit_dept_code" name="code" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeEditDeptModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Tambah Program Studi -->
<div id="prodiModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <div>
                <h3 class="text-[16px] font-bold text-text-primary">Tambah Program Studi</h3>
                <p id="prodi_dept_label" class="text-[11px] text-text-secondary mt-0.5"></p>
            </div>
            <button type="button" onclick="closeProdiModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.academic-structures.study-programs.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="prodi_dept_id" name="department_id" value="">
            <div>
                <label for="prodi_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Program Studi <span class="text-error">*</span></label>
                <input type="text" id="prodi_name" name="name" required placeholder="Contoh: Manajemen Informatika" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="prodi_level" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Jenjang Pendidikan <span class="text-error">*</span></label>
                <select id="prodi_level" name="degree_level" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                    <option value="D3">Diploma 3 (D3)</option>
                    <option value="Sarjana Terapan">Sarjana Terapan (D4)</option>
                    <option value="Magister Terapan">Magister Terapan (S2)</option>
                </select>
            </div>
            <div>
                <label for="prodi_code" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Kode Prodi</label>
                <input type="text" id="prodi_code" name="code" placeholder="Contoh: MI, TRPL" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeProdiModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Program Studi</button>
            </div>
        </form>
    </div>
</div>

<!-- 2b. Modal Ubah Program Studi -->
<div id="editProdiModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Ubah Data Program Studi</h3>
            <button type="button" onclick="closeEditProdiModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editProdiForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_prodi_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Program Studi <span class="text-error">*</span></label>
                <input type="text" id="edit_prodi_name" name="name" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="edit_prodi_level" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Jenjang Pendidikan <span class="text-error">*</span></label>
                <select id="edit_prodi_level" name="degree_level" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                    <option value="D3">Diploma 3 (D3)</option>
                    <option value="Sarjana Terapan">Sarjana Terapan (D4)</option>
                    <option value="Magister Terapan">Magister Terapan (S2)</option>
                </select>
            </div>
            <div>
                <label for="edit_prodi_code" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Kode Prodi</label>
                <input type="text" id="edit_prodi_code" name="code" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeEditProdiModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Tambah Kelas & Angkatan -->
<div id="classModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <div>
                <h3 class="text-[16px] font-bold text-text-primary">Tambah Kelas Mahasiswa</h3>
                <p id="class_prodi_label" class="text-[11px] text-text-secondary mt-0.5"></p>
            </div>
            <button type="button" onclick="closeClassModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.academic-structures.classes.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="class_prodi_id" name="study_program_id" value="">
            <div>
                <label for="class_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Kelas <span class="text-error">*</span></label>
                <input type="text" id="class_name" name="name" required placeholder="Contoh: MI 3A, TRPL 1B" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="cohort_year" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Tahun Angkatan <span class="text-error">*</span></label>
                <input type="number" id="cohort_year" name="cohort_year" required min="2020" max="2035" value="{{ date('Y') }}" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeClassModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<!-- 3b. Modal Ubah Kelas & Angkatan -->
<div id="editClassModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Ubah Data Kelas</h3>
            <button type="button" onclick="closeEditClassModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editClassForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_class_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Kelas <span class="text-error">*</span></label>
                <input type="text" id="edit_class_name" name="name" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="edit_cohort_year" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Tahun Angkatan <span class="text-error">*</span></label>
                <input type="number" id="edit_cohort_year" name="cohort_year" required min="2020" max="2035" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeEditClassModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px]">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Jurusan Modals
    function openDeptModal() { document.getElementById('deptModal').classList.remove('hidden'); }
    function closeDeptModal() { document.getElementById('deptModal').classList.add('hidden'); }

    function openEditDeptModal(id, name, code) {
        document.getElementById('editDeptForm').action = '/struktur-akademik/department/' + id;
        document.getElementById('edit_dept_name').value = name;
        document.getElementById('edit_dept_code').value = code;
        document.getElementById('editDeptModal').classList.remove('hidden');
    }
    function closeEditDeptModal() { document.getElementById('editDeptModal').classList.add('hidden'); }

    // Prodi Modals
    function openProdiModal(deptId, deptName) {
        document.getElementById('prodi_dept_id').value = deptId;
        document.getElementById('prodi_dept_label').innerText = 'Jurusan: ' + deptName;
        document.getElementById('prodiModal').classList.remove('hidden');
    }
    function closeProdiModal() { document.getElementById('prodiModal').classList.add('hidden'); }

    function openEditProdiModal(id, name, level, code) {
        document.getElementById('editProdiForm').action = '/struktur-akademik/study-program/' + id;
        document.getElementById('edit_prodi_name').value = name;
        document.getElementById('edit_prodi_level').value = level;
        document.getElementById('edit_prodi_code').value = code;
        document.getElementById('editProdiModal').classList.remove('hidden');
    }
    function closeEditProdiModal() { document.getElementById('editProdiModal').classList.add('hidden'); }

    // Kelas Modals
    function openClassModal(prodiId, prodiName) {
        document.getElementById('class_prodi_id').value = prodiId;
        document.getElementById('class_prodi_label').innerText = 'Program Studi: ' + prodiName;
        document.getElementById('classModal').classList.remove('hidden');
    }
    function closeClassModal() { document.getElementById('classModal').classList.add('hidden'); }

    function openEditClassModal(id, name, cohort) {
        document.getElementById('editClassForm').action = '/struktur-akademik/class/' + id;
        document.getElementById('edit_class_name').value = name;
        document.getElementById('edit_cohort_year').value = cohort;
        document.getElementById('editClassModal').classList.remove('hidden');
    }
    function closeEditClassModal() { document.getElementById('editClassModal').classList.add('hidden'); }
</script>
@endpush
@endsection
