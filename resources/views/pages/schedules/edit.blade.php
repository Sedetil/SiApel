@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Ubah Jadwal Apel
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Perbarui parameter waktu dan status sesi apel.
            </p>
        </div>
        <a href="{{ route('schedules.index') }}" class="text-[13px] font-medium text-text-secondary hover:text-text-primary">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-surface border border-border rounded-lg p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('schedules.update', $schedule) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Title & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="title" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Nama / Judul Apel <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title', $schedule->title) }}" 
                        required 
                        class="w-full text-[14px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                    @error('title')
                        <p class="text-[12px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="category" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Kategori Apel <span class="text-error">*</span>
                    </label>
                    <select id="category" name="category" required class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                        <option value="rutin" {{ old('category', $schedule->category) === 'rutin' ? 'selected' : '' }}>Rutin</option>
                        <option value="tambahan" {{ old('category', $schedule->category) === 'tambahan' ? 'selected' : '' }}>Tambahan</option>
                        <option value="khusus" {{ old('category', $schedule->category) === 'khusus' ? 'selected' : '' }}>Khusus</option>
                    </select>
                    @error('category')
                        <p class="text-[12px] text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Schedule Date & Location -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="schedule_date" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Tanggal Apel <span class="text-error">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="schedule_date" 
                        name="schedule_date" 
                        value="{{ old('schedule_date', $schedule->schedule_date->format('Y-m-d')) }}" 
                        required 
                        class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                    >
                </div>

                <div>
                    <label for="location" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Lokasi Apel <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        value="{{ old('location', $schedule->location) }}" 
                        required 
                        class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                    >
                </div>
            </div>

            <!-- Times: Apel Start & End -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Waktu Mulai Apel <span class="text-error">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="start_time" 
                        name="start_time" 
                        value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}" 
                        required 
                        class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                    >
                </div>

                <div>
                    <label for="end_time" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                        Waktu Selesai Apel <span class="text-error">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="end_time" 
                        name="end_time" 
                        value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}" 
                        required 
                        class="w-full font-mono text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                    >
                </div>
            </div>

            <!-- Attendance Windows & Late Threshold -->
            <div class="p-4 bg-background rounded-md border border-border space-y-4">
                <span class="text-[12px] font-bold uppercase tracking-wider text-text-primary block">
                    Aturan Waktu Absensi & Toleransi Keterlambatan
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label for="attendance_start" class="block text-[11px] font-bold text-text-secondary uppercase mb-1">
                            Buka Presensi
                        </label>
                        <input 
                            type="time" 
                            id="attendance_start" 
                            name="attendance_start" 
                            value="{{ old('attendance_start', substr($schedule->attendance_start, 0, 5)) }}" 
                            required 
                            class="w-full font-mono text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>

                    <div>
                        <label for="late_threshold_time" class="block text-[11px] font-bold text-warning uppercase mb-1">
                            Batas Tepat Waktu
                        </label>
                        <input 
                            type="time" 
                            id="late_threshold_time" 
                            name="late_threshold_time" 
                            value="{{ old('late_threshold_time', substr($schedule->late_threshold_time, 0, 5)) }}" 
                            required 
                            class="w-full font-mono text-[13px] px-3 py-2 bg-surface border border-warning/40 text-text-primary focus:outline-none focus:border-warning"
                        >
                    </div>

                    <div>
                        <label for="attendance_end" class="block text-[11px] font-bold text-text-secondary uppercase mb-1">
                            Tutup Presensi
                        </label>
                        <input 
                            type="time" 
                            id="attendance_end" 
                            name="attendance_end" 
                            value="{{ old('attendance_end', substr($schedule->attendance_end, 0, 5)) }}" 
                            required 
                            class="w-full font-mono text-[13px] px-3 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                        >
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    Status Sesi
                </label>
                <select id="status" name="status" class="w-full text-[13px] px-3.5 py-2.5 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary">
                    <option value="scheduled" {{ old('status', $schedule->status) === 'scheduled' ? 'selected' : '' }}>Terjadwal (Scheduled)</option>
                    <option value="active" {{ old('status', $schedule->status) === 'active' ? 'selected' : '' }}>Aktif (Sedang Berlangsung)</option>
                    <option value="completed" {{ old('status', $schedule->status) === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ old('status', $schedule->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-[12px] font-bold text-text-primary uppercase tracking-wider mb-2">
                    Keterangan Tambahan
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="2" 
                    class="w-full text-[13px] px-3.5 py-2 bg-surface border border-border rounded-md text-text-primary focus:outline-none focus:border-primary"
                >{{ old('description', $schedule->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('schedules.index') }}" class="px-4 py-2.5 border border-border text-text-secondary hover:bg-background rounded-md text-[13px] font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-md text-[13px] font-medium transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
