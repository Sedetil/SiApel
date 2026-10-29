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
                Daftarkan peserta apel baru dan petakan kartu RFID jika tersedia.
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

            <!-- Program Studi & Kelas Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="study_program" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Program Studi / Jurusan
                    </label>
                    <input 
                        type="text" 
                        id="study_program" 
                        name="study_program" 
                        value="{{ old('study_program') }}" 
                        placeholder="Contoh: Manajemen Informatika"
                        class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div>
                    <label for="class_group" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Kompi / Pleton / Kelas
                    </label>
                    <input 
                        type="text" 
                        id="class_group" 
                        name="class_group" 
                        value="{{ old('class_group') }}" 
                        placeholder="Contoh: Kompi A - Pleton 1"
                        class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
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
@endsection
