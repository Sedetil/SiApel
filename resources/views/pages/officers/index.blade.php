@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Pengelolaan Akun Timdis
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Kelola akun timdis operasional yang berwenang memantau dan memvalidasi absensi apel.
            </p>
        </div>

        <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-4 py-2.5 rounded-md text-[13px] font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Timdis</span>
        </button>
    </div>

    <!-- Officers Table Card -->
    <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
        @if($officers->isEmpty())
            <div class="p-12 text-center text-text-secondary text-[14px]">
                <p class="font-bold text-text-primary">Belum Ada Akun Timdis</p>
                <p class="text-[12px] mt-0.5">Silakan tambahkan akun timdis baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-background border-b border-border text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        <tr>
                            <th class="py-3 px-4">Nama Timdis</th>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4">Program Studi</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Waktu Login Terakhir</th>
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
                                <td class="py-3 px-4 text-slate-700 font-medium">
                                    {{ $officer->study_program ?? '-' }}
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
                                            onclick="openEditModal('{{ $officer->id }}', '{{ addslashes($officer->name) }}', '{{ $officer->username }}', '{{ $officer->email }}', '{{ addslashes($officer->study_program ?? '') }}')"
                                            class="p-1.5 rounded-md hover:bg-background text-text-secondary hover:text-text-primary transition-colors"
                                            title="Ubah Akun"
                                        >
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Toggle Status (Nonaktifkan) with confirmation -->
                                        <form method="POST" action="{{ route('officers.toggle-status', $officer) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif akun Timdis {{ $officer->name }}?')">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-md hover:bg-background {{ $officer->isActive() ? 'text-text-secondary hover:text-warning' : 'text-success' }} transition-colors" title="{{ $officer->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <i data-lucide="power" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                        <!-- Delete Akun (Hapus) with confirmation dialog -->
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
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Tambah Akun Timdis</h3>
            <button type="button" onclick="closeCreateModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('officers.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="create_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Nama Timdis <span class="text-error">*</span>
                </label>
                <input type="text" id="create_name" name="name" required placeholder="Contoh: Budi Santoso" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="create_username" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Username Login <span class="text-error">*</span>
                </label>
                <input type="text" id="create_username" name="username" required placeholder="Contoh: timdis_kompi_a" class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="create_email" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Email Resmi <span class="text-error">*</span>
                </label>
                <input type="email" id="create_email" name="email" required placeholder="timdis@polinela.ac.id" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="create_study_program" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Program Studi / Jurusan
                </label>
                <input type="text" id="create_study_program" name="study_program" placeholder="Contoh: Manajemen Informatika" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="create_password" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Kata Sandi Awal <span class="text-error">*</span>
                </label>
                <input type="password" id="create_password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px] font-medium transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                    Buat Akun Timdis
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Timdis -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Ubah Data Timdis</h3>
            <button type="button" onclick="closeEditModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_name" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Nama Timdis</label>
                <input type="text" id="edit_name" name="name" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="edit_username" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Username Login</label>
                <input type="text" id="edit_username" name="username" required class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="edit_study_program" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Program Studi</label>
                <input type="text" id="edit_study_program" name="study_program" placeholder="Contoh: Manajemen Informatika" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="edit_email" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" id="edit_email" name="email" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div>
                <label for="edit_password" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Ganti Kata Sandi (Kosongkan jika tidak diubah)
                </label>
                <input type="password" id="edit_password" name="password" minlength="6" placeholder="Kosongkan jika tetap" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px] font-medium transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }
    function openEditModal(id, name, username, email, studyProgram) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_study_program').value = studyProgram || '';
        document.getElementById('editForm').action = `/officers/${id}`;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
