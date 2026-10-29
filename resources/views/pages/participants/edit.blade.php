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
                Perbarui biodata peserta dan kelola kartu RFID yang terhubung.
            </p>
        </div>
        <a href="{{ route('admin.participants.index') }}" class="text-[13px] font-medium text-text-secondary hover:text-text-primary">
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
                        NPM Mahasiswa <span class="text-error">*</span>
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

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="study_program" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                            Program Studi
                        </label>
                        <input 
                            type="text" 
                            id="study_program" 
                            name="study_program" 
                            value="{{ old('study_program', $participant->study_program) }}" 
                            class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>

                    <div>
                        <label for="class_group" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-1.5">
                            Kompi / Kelas
                        </label>
                        <input 
                            type="text" 
                            id="class_group" 
                            name="class_group" 
                            value="{{ old('class_group', $participant->class_group) }}" 
                            class="w-full text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
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
                    <div class="p-4 rounded-md bg-primary/5 border border-primary/20 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-primary">Kartu Aktif</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-success/15 text-success">
                                Terverifikasi
                            </span>
                        </div>
                        <div class="font-mono text-[22px] font-bold text-text-primary tracking-wide">
                            {{ $participant->activeRfidCard->card_uid }}
                        </div>
                        <div class="text-[11px] text-text-secondary space-y-0.5">
                            <div>Diterbitkan: <strong>{{ $participant->activeRfidCard->issued_at->format('d/m/Y H:i') }}</strong></div>
                            @if($participant->activeRfidCard->notes)
                                <div>Catatan: {{ $participant->activeRfidCard->notes }}</div>
                            @endif
                        </div>

                        <!-- Deactivate Button -->
                        <form method="POST" action="{{ route('admin.participants.deactivate-rfid', $participant) }}" class="pt-2" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kartu RFID ini?')">
                            @csrf
                            <button type="submit" class="w-full py-1.5 px-3 rounded-md bg-error/10 hover:bg-error/20 text-error text-[12px] font-medium transition-colors">
                                Nonaktifkan Kartu Ini
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-5 text-center bg-background rounded-md border border-dashed border-border text-[13px] text-text-secondary">
                        <i data-lucide="credit-card" class="w-8 h-8 mx-auto text-text-secondary/50 mb-2"></i>
                        <p class="font-bold text-text-primary">Belum Ada Kartu Aktif</p>
                        <p class="text-[12px] mt-0.5">Petakan kartu RFID di bawah agar peserta dapat melakukan absensi.</p>
                    </div>
                @endif

                <!-- Map New Card Form -->
                <div class="mt-4 pt-4 border-t border-border">
                    <h3 class="text-[13px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        {{ $participant->activeRfidCard ? 'Ganti dengan Kartu Baru' : 'Petakan Kartu RFID' }}
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.participants.map-rfid', $participant) }}" class="space-y-3">
                        @csrf
                        <div>
                            <input 
                                type="text" 
                                name="card_uid" 
                                required 
                                placeholder="Tempelkan kartu atau masukkan UID..."
                                class="w-full font-mono font-bold text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                            >
                        </div>
                        <div>
                            <input 
                                type="text" 
                                name="notes" 
                                placeholder="Catatan penerbitan (opsional)..."
                                class="w-full text-[12px] px-3 py-1.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                            >
                        </div>
                        <button type="submit" class="w-full py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                            Konfirmasi Pemetaan Kartu
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card History -->
            @if($participant->rfidCards->where('is_active', false)->isNotEmpty())
                <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
                    <h3 class="text-[13px] font-bold text-text-secondary uppercase tracking-wider mb-2">Riwayat Kartu Lama</h3>
                    <div class="space-y-2">
                        @foreach($participant->rfidCards->where('is_active', false) as $oldCard)
                            <div class="p-2.5 rounded-md border border-border bg-background text-[11px] text-text-secondary">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-text-primary">{{ $oldCard->card_uid }}</span>
                                    <span class="text-error font-bold">Nonaktif</span>
                                </div>
                                <div class="text-[10px] mt-0.5">
                                    Dinonaktifkan: {{ $oldCard->deactivated_at?->format('d/m/Y') ?? '-' }} &bull; {{ $oldCard->notes ?? 'Diganti' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
