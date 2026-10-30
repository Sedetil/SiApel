@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold text-text-primary tracking-tight">
                Log Audit Sistem (Audit Trail)
            </h1>
            <p class="text-[14px] text-text-secondary mt-1">
                Rekaman riwayat seluruh aktivitas penting, mutasi data, dan autentikasi untuk akuntabilitas sistem.
            </p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label for="search" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Cari Deskripsi / User / IP</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Kata kunci..."
                    class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:border-primary"
                >
            </div>

            <div>
                <label for="action" class="block text-[11px] font-bold uppercase tracking-wider text-text-secondary mb-1">Jenis Tindakan (Action)</label>
                <select id="action" name="action" class="w-full bg-surface border border-border text-text-primary text-[13px] rounded-md px-3 py-2 focus:outline-none focus:border-primary">
                    <option value="">Semua Tindakan</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white text-[13px] font-medium py-2 px-3 rounded-md transition-colors shadow-sm flex items-center justify-center gap-1.5">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.audit-logs.index') }}" class="bg-background hover:bg-border/40 text-text-secondary text-[13px] py-2 px-3 rounded-md transition-colors border border-border">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table Card -->
    <div class="bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-12 text-center text-text-secondary text-[14px]">
                <p class="font-bold text-text-primary">Tidak Ada Log Tercatat</p>
                <p class="text-[12px] mt-0.5">Belum ada aktivitas yang terekam pada audit log sistem.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-background border-b border-border text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        <tr>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Pelaku (User)</th>
                            <th class="py-3 px-4">Tindakan</th>
                            <th class="py-3 px-4">Deskripsi Aktivitas</th>
                            <th class="py-3 px-4">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach($logs as $log)
                            <tr class="hover:bg-background/40 transition-colors">
                                <td class="py-3 px-4 font-mono text-[12px] text-text-secondary whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-text-primary">{{ $log->user?->name ?? 'Sistem / Anonim' }}</div>
                                    <div class="text-[11px] text-text-secondary">{{ $log->user?->role ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-mono font-bold bg-primary/10 text-primary border border-primary/20">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-text-primary text-[13px]">
                                    {{ $log->description }}
                                </td>
                                <td class="py-3 px-4 font-mono text-[12px] text-text-secondary whitespace-nowrap">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-border bg-surface">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
