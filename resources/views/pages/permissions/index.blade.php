@extends('layouts.app')

@php
    $breadcrumbCategory = 'Menu Utama';
    $breadcrumbPage = 'Izin & Sakit';
@endphp

@section('content')
<div class="space-y-4 max-w-[1240px] mx-auto pb-8">

    <!-- 2. JUDUL HALAMAN & SUB-JUDUL -->
    <div class="pt-1 pb-1">
        <h1 class="text-[26px] font-bold text-slate-900 tracking-tight leading-tight">
            Pengajuan izin
        </h1>
        <p class="text-[13.5px] text-slate-500 mt-0.5">
            Tinjau dan kelola pengajuan izin Sakit Mahasiswa
        </p>
    </div>

    <!-- Feedback Flash Messages jika ada update status -->
    @if (session('status'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[12.5px] font-medium flex items-center gap-2.5 shadow-xs">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- 3. BANNER PERINGATAN (ALERT BOX) -->
    @include('pages.permissions.alert-banner')

    <!-- 4. & 5. CARD UTAMA & DAFTAR PENGAJUAN -->
    @include('pages.permissions.table-list')

    <!-- 6. PAGINATION (BAWAH TENGAH) -->
    @include('pages.permissions.pagination')

</div>

<!-- Interactive Detail Modal -->
@include('pages.permissions.detail-modal')

@push('scripts')
<script>
    function openDetailModal(req) {
        if (!req) return;

        const studentName = req.participant ? req.participant.name : (req.name || '-');
        const studentNpm = req.participant 
            ? (req.participant.identifier + ' • ' + (req.participant.study_program || '-')) 
            : ((req.npm ? req.npm + ' • ' : '') + (req.program || '-'));

        document.getElementById('modal-student-name').textContent = studentName;
        document.getElementById('modal-student-npm').textContent = studentNpm;
        
        const type = (req.type || 'sakit').toLowerCase();
        const typeBadge = document.getElementById('modal-type-badge');
        typeBadge.textContent = type.toUpperCase();
        typeBadge.className = type === 'sakit' 
            ? 'px-2.5 py-1 rounded text-[11px] font-bold bg-amber-100 text-amber-800' 
            : 'px-2.5 py-1 rounded text-[11px] font-bold bg-indigo-100 text-indigo-800';

        document.getElementById('modal-schedule').textContent = req.schedule 
            ? `${req.schedule.title} (${req.schedule.schedule_date})` 
            : 'Apel Kedisiplinan Polinela';

        document.getElementById('modal-reason').textContent = req.reason || 'Demam tinggi dan istirahat dokter sesuai rekomendasi.';
        
        const attachName = document.getElementById('modal-attachment-name');
        attachName.textContent = req.attachment_path || (req.attachment || 'surat_keterangan_sakit.pdf');

        const decisionBox = document.getElementById('modal-decision-box');
        const decisionStatus = document.getElementById('modal-decision-status');
        const decisionDate = document.getElementById('modal-decision-date');
        const decisionNotes = document.getElementById('modal-decision-notes');
        const decisionOfficer = document.getElementById('modal-decision-officer');
        const decisionForm = document.getElementById('modal-decision-form');

        const status = req.status || 'menunggu';
        decisionStatus.textContent = status.toUpperCase();
        decisionDate.textContent = req.decided_at ? req.decided_at : '-';
        decisionNotes.textContent = req.notes ? `"${req.notes}"` : (status === 'menunggu' ? 'Menunggu verifikasi oleh Timdis penanggung jawab.' : 'Tanpa catatan.');
        decisionOfficer.textContent = req.decider ? `Diverifikasi oleh: ${req.decider.name}` : 'Penanggung jawab: Petugas Timdis';

        if (status === 'menunggu') {
            decisionBox.className = 'p-3 rounded-lg border border-amber-200 bg-amber-50/50 text-[12px] space-y-1';
            decisionForm.classList.remove('hidden');
            decisionForm.action = req.id ? `/izin-sakit/${req.id}/decide` : '#';
        } else if (status === 'disetujui') {
            decisionBox.className = 'p-3 rounded-lg border border-emerald-200 bg-emerald-50/50 text-[12px] space-y-1';
            decisionForm.classList.add('hidden');
        } else {
            decisionBox.className = 'p-3 rounded-lg border border-red-200 bg-red-50/50 text-[12px] space-y-1';
            decisionForm.classList.add('hidden');
        }

        document.getElementById('detail-modal').classList.remove('hidden');
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
