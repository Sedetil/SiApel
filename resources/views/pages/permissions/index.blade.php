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

        const studentName = req.participant ? req.participant.name : (req.name || 'Yoga Saputra');
        const studentProgram = req.participant 
            ? (req.participant.study_program || 'Teknologi Rekayasa Perangkat Lunak') 
            : (req.program || 'Teknologi Rekayasa Perangkat Lunak');

        const nameParts = studentName.trim().split(' ');
        let initials = (nameParts[0] ? nameParts[0][0] : 'Y') + (nameParts[1] ? nameParts[1][0] : 'S');
        initials = initials.toUpperCase();

        document.getElementById('modal-avatar').textContent = initials;
        document.getElementById('modal-student-name').textContent = studentName;
        document.getElementById('modal-student-program').textContent = studentProgram;
        
        const profileLink = document.getElementById('modal-profile-link');
        const participantId = req.participant_id || (req.participant ? req.participant.id : null);
        profileLink.href = participantId ? `/participants/${participantId}` : '#';

        const type = (req.type || 'sakit').toLowerCase();
        document.getElementById('modal-type-label').textContent = type === 'sakit' ? 'Izin Sakit' : 'Izin Keperluan';

        const scheduleTitle = req.schedule ? req.schedule.title : 'Apel di hari senin';
        const scheduleDate = req.schedule && req.schedule.schedule_date 
            ? req.schedule.schedule_date 
            : (req.date_range || 'Minggu, 21 Oktober 2026');
        
        document.getElementById('modal-schedule-title').textContent = scheduleTitle;
        document.getElementById('modal-schedule-date').textContent = scheduleDate;

        document.getElementById('modal-reason').textContent = req.reason || 'Selamat pagi, mohon izin menyampaikan bahwa hari ini saya tidak dapat mengikuti apel dikarenakan kondisi badan sedang kurang sehat/sakit. Mohon izin untuk beristirahat. Terima kasih.';
        
        const attachName = document.getElementById('modal-attachment-name');
        const attachLink = document.getElementById('modal-attachment-link');
        const hasAttach = req.attachment_path || req.attachment;
        if (hasAttach) {
            attachName.textContent = req.attachment_path || req.attachment;
            attachLink.classList.remove('hidden');
            attachLink.href = `/storage/${req.attachment_path || req.attachment}`;
        } else {
            attachName.textContent = 'Tidak ada lampiran yang disertakan.';
            attachLink.classList.add('hidden');
        }

        const status = (req.status || 'menunggu').toLowerCase();
        const statusBadge = document.getElementById('modal-status-badge');
        const statusText = document.getElementById('modal-status-text');
        const decisionForm = document.getElementById('modal-decision-form');
        const actionButtons = document.getElementById('modal-action-buttons');
        const footerCaption = document.getElementById('modal-footer-caption');
        const notes = document.getElementById('modal-notes');

        statusText.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        notes.value = req.notes || '';

        const timelineApplicant = document.getElementById('modal-timeline-applicant');
        const timelineTime = document.getElementById('modal-timeline-time');
        const timelineDecision = document.getElementById('modal-timeline-decision');
        const timelineDot = document.getElementById('modal-timeline-decision-dot');
        const timelineText = document.getElementById('modal-timeline-decision-text');

        timelineApplicant.textContent = `Diajukan oleh ${studentName}`;
        timelineTime.textContent = req.submit_time || '20 Okt 2026 · 16.30 WIB';

        if (status === 'menunggu') {
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/90';
            timelineDot.className = 'w-2 h-2 rounded-full bg-amber-500 shrink-0';
            timelineText.className = 'text-amber-700 font-medium';
            timelineText.textContent = 'Menunggu persetujuan Admin SDM';

            decisionForm.action = req.id ? `/izin-sakit/${req.id}/decide` : '#';
            actionButtons.classList.remove('hidden');
            footerCaption.classList.remove('hidden');
            notes.removeAttribute('readonly');
        } else if (status === 'disetujui') {
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/90';
            timelineDot.className = 'w-2 h-2 rounded-full bg-emerald-500 shrink-0';
            timelineText.className = 'text-emerald-700 font-medium';
            timelineText.textContent = `Disetujui oleh ${req.decider ? req.decider.name : 'Admin Timdis'}`;

            actionButtons.classList.add('hidden');
            footerCaption.classList.add('hidden');
            notes.setAttribute('readonly', 'readonly');
        } else {
            statusBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-red-50 text-red-700 border border-red-200/90';
            timelineDot.className = 'w-2 h-2 rounded-full bg-red-500 shrink-0';
            timelineText.className = 'text-red-700 font-medium';
            timelineText.textContent = `Ditolak oleh ${req.decider ? req.decider.name : 'Admin Timdis'}`;

            actionButtons.classList.add('hidden');
            footerCaption.classList.add('hidden');
            notes.setAttribute('readonly', 'readonly');
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
