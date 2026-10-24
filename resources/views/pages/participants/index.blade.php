@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Data Peserta Apel
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Kelola data mahasiswa kampus dan pemetaan identitas kartu RFID.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Sinkron dari E-Kuliah (Action from IA) -->
            <form method="POST" action="{{ route('participants.sync-ekuliah') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 px-3.5 py-2.5 rounded-lg text-[13px] font-semibold transition-colors shadow-xs">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-600"></i>
                    <span>Sinkron dari E-Kuliah</span>
                </button>
            </form>

            <a href="{{ route('participants.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-4 py-2.5 rounded-lg text-[13px] font-semibold transition-colors shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Mahasiswa</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
        <form method="GET" action="{{ route('participants.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label for="search" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Cari Peserta / UID</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Nama, NPM, atau UID RFID..."
                    class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >
            </div>

            <div>
                <label for="study_program" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Program Studi</label>
                <select 
                    id="study_program" 
                    name="study_program" 
                    class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >
                    <option value="">Semua Program Studi</option>
                    @foreach($studyPrograms as $p)
                        <option value="{{ $p }}" {{ request('study_program') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Status Keaktifan</label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white text-[13px] font-medium py-2 px-3 rounded-md transition-colors shadow-sm flex items-center justify-center gap-1.5">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('participants.index') }}" class="bg-background hover:bg-border/40 text-text-secondary text-[13px] py-2 px-3 rounded-md transition-colors border border-border">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Participants Table Card -->
    <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
        @if($participants->isEmpty())
            <div class="p-12 text-center text-text-secondary text-[14px]">
                <p class="font-bold text-text-primary">Tidak Ada Data Peserta</p>
                <p class="text-[12px] mt-0.5">Tidak ditemukan data peserta sesuai kriteria pencarian.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-background border-b border-border text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        <tr>
                            <th class="py-3 px-4">NPM / NIM</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Program Studi</th>
                            <th class="py-3 px-4">Kompi / Kelas</th>
                            <th class="py-3 px-4">Kartu RFID Terhubung</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach($participants as $p)
                            <tr class="hover:bg-background/40 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-text-primary">
                                    {{ $p->identifier }}
                                </td>
                                <td class="py-3 px-4 font-bold text-text-primary">
                                    {{ $p->name }}
                                </td>
                                <td class="py-3 px-4 text-text-secondary">
                                    {{ $p->study_program ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-text-secondary text-[12px]">
                                    {{ $p->class_group ?? '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($p->activeRfidCard)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-sm text-[11px] font-mono font-bold bg-primary/10 text-primary border border-primary/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                            {{ $p->activeRfidCard->card_uid }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-border/40 text-text-secondary">
                                            Belum Ada Kartu
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($p->isActive())
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
                                        <!-- Map RFID Quick Trigger Button -->
                                        <button 
                                            type="button" 
                                            onclick="openMapModal('{{ $p->id }}', '{{ $p->name }}', '{{ $p->identifier }}', '{{ $p->activeRfidCard?->card_uid }}')"
                                            class="p-1.5 rounded-md hover:bg-background text-primary transition-colors" 
                                            title="Petakan Kartu RFID"
                                        >
                                            <i data-lucide="key-round" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Edit -->
                                        <a href="{{ route('participants.edit', $p) }}" class="p-1.5 rounded-md hover:bg-background text-text-secondary hover:text-text-primary transition-colors" title="Ubah Data">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ route('participants.toggle-status', $p) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status peserta ini?')">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-md hover:bg-background {{ $p->isActive() ? 'text-text-secondary hover:text-warning' : 'text-success' }} transition-colors" title="{{ $p->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i data-lucide="power" class="w-4 h-4"></i>
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
                {{ $participants->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Dialog Pemetaan Kartu RFID (Radius 12px) -->
<div id="mapModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-text-primary/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface border border-border rounded-lg shadow-xl max-w-md w-full p-6 animate-scaleUp">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border">
            <h3 class="text-[16px] font-bold text-text-primary">Pemetaan Kartu RFID</h3>
            <button type="button" onclick="closeMapModal()" class="text-text-secondary hover:text-text-primary p-1 rounded-md">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="mapForm" method="POST" action="" class="space-y-4">
            @csrf
            
            <div class="p-3 bg-background rounded-md border border-border text-[12px] space-y-1">
                <div class="flex justify-between">
                    <span class="text-text-secondary">Peserta:</span>
                    <strong id="modal-participant-name" class="text-text-primary"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-secondary">NPM / NIM:</span>
                    <span id="modal-participant-npm" class="font-mono font-bold text-text-primary"></span>
                </div>
                <div class="flex justify-between" id="current-card-row">
                    <span class="text-text-secondary">Kartu Saat Ini:</span>
                    <span id="modal-current-card" class="font-mono text-primary font-bold"></span>
                </div>
            </div>

            <div>
                <label for="modal_card_uid" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    UID Kartu RFID Baru
                </label>
                <input 
                    type="text" 
                    id="modal_card_uid" 
                    name="card_uid" 
                    required 
                    autocomplete="off"
                    placeholder="Contoh: E2000030 (Tempelkan ke reader)"
                    class="w-full font-mono font-bold text-[15px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                >
                <p class="text-[11px] text-text-secondary mt-1">
                    UID harus unik dan belum terhubung ke peserta aktif lain.
                </p>
            </div>

            <div>
                <label for="modal_notes" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">
                    Catatan (Opsional)
                </label>
                <input 
                    type="text" 
                    id="modal_notes" 
                    name="notes" 
                    placeholder="Alasan pergantian atau penerbitan kartu..."
                    class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                <button type="button" onclick="closeMapModal()" class="px-4 py-2 border border-border text-text-secondary hover:bg-background rounded-md text-[13px] font-medium transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                    Simpan Pemetaan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMapModal(id, name, npm, currentCard) {
        document.getElementById('modal-participant-name').innerText = name;
        document.getElementById('modal-participant-npm').innerText = npm;
        
        const cardSpan = document.getElementById('modal-current-card');
        const cardRow = document.getElementById('current-card-row');
        if (currentCard && currentCard !== '') {
            cardSpan.innerText = currentCard;
            cardRow.classList.remove('hidden');
        } else {
            cardRow.classList.add('hidden');
        }

        const form = document.getElementById('mapForm');
        form.action = `/participants/${id}/map-rfid`;

        document.getElementById('mapModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('modal_card_uid').focus(), 100);
    }

    function closeMapModal() {
        document.getElementById('mapModal').classList.add('hidden');
        document.getElementById('modal_card_uid').value = '';
        document.getElementById('modal_notes').value = '';
    }
</script>
@endsection
