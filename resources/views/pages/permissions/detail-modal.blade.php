<!-- INTERACTIVE DETAIL & APPROVAL MODAL -->
<div id="detail-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all my-8">
        
        <!-- Modal Top Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <h3 class="text-[16px] font-bold text-slate-900 leading-tight">Detail pengajuan</h3>
            <div class="flex items-center gap-3">
                <span id="modal-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/90">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span id="modal-status-text">Menunggu</span>
                </span>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 space-y-5 text-[13px] max-h-[80vh] overflow-y-auto">
            
            <!-- Identitas Mahasiswa -->
            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center gap-3.5">
                    <div id="modal-avatar" class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-600 font-bold text-[13.5px] flex items-center justify-center shrink-0 shadow-xs">
                        YS
                    </div>
                    <div>
                        <h4 id="modal-student-name" class="text-[15px] font-bold text-slate-900 leading-snug">
                            Yoga Saputra
                        </h4>
                        <p id="modal-student-program" class="text-[12.5px] text-slate-500 font-normal">
                            Teknologi Rekayasa Perangkat Lunak
                        </p>
                    </div>
                </div>
                <a id="modal-profile-link" href="#" class="text-[12.5px] font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1 transition-colors">
                    <span>Lihat profil</span>
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Box Detail Pengajuan (Jenis, Durasi, Tanggal Apel) -->
            <div class="border border-slate-200/90 rounded-xl p-4 bg-white space-y-4 shadow-2xs">
                <!-- Grid 2 Columns -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[11.5px] text-slate-400 font-medium">Jenis pengajuan</span>
                        <span id="modal-type-label" class="block text-[14px] font-bold text-slate-900 mt-0.5">
                            Izin Sakit
                        </span>
                    </div>
                    <div>
                        <span class="block text-[11.5px] text-slate-400 font-medium">Durasi</span>
                        <div class="mt-0.5">
                            <span class="text-[14px] font-bold text-slate-900">1 hari</span>
                            <span class="block text-[11px] text-slate-400 font-normal">1 hari apel</span>
                        </div>
                    </div>
                </div>

                <!-- Calendar Row -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div id="modal-schedule-date" class="text-[13px] font-bold text-slate-900">
                                Minggu, 21 Oktober 2026
                            </div>
                            <div id="modal-schedule-title" class="text-[11.5px] text-slate-400">
                                Apel di hari senin
                            </div>
                        </div>
                    </div>
                    <span id="modal-badge-timing" class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200/80">
                        Besok
                    </span>
                </div>
            </div>

            <!-- Alasan Pengajuan -->
            <div class="space-y-2 pt-1">
                <h5 class="text-[13px] font-bold text-slate-900">Alasan pengajuan</h5>
                <p id="modal-reason" class="text-[13px] text-slate-700 leading-relaxed font-normal">
                    Selamat pagi, mohon izin menyampaikan bahwa hari ini saya tidak dapat mengikuti apel dikarenakan kondisi badan sedang kurang sehat/sakit. Mohon izin untuk beristirahat. Terima kasih.
                </p>

                <!-- Bukti Dokumen Lampiran -->
                <div class="border border-slate-200/90 rounded-xl p-3 bg-white flex items-center justify-between text-[12.5px] mt-2">
                    <div class="flex items-center gap-2.5 text-slate-500">
                        <i data-lucide="paperclip" class="w-4 h-4 text-slate-400 shrink-0"></i>
                        <span id="modal-attachment-name" class="font-normal text-slate-500">Tidak ada lampiran yang disertakan.</span>
                    </div>
                    <a id="modal-attachment-link" href="#" target="_blank" class="hidden text-[12px] font-semibold text-blue-600 hover:underline flex items-center gap-1">
                        <span>Lihat lampiran</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Riwayat Pengajuan (Timeline) -->
            <div class="space-y-2.5 pt-1">
                <h5 class="text-[13px] font-bold text-slate-900">Riwayat pengajuan</h5>
                <div class="space-y-2 text-[12.5px]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: #2D56FF;"></span>
                            <span id="modal-timeline-applicant" class="text-slate-800 font-normal">Diajukan oleh Yoga Saputra</span>
                        </div>
                        <span id="modal-timeline-time" class="text-slate-400 text-[11.5px] font-mono">20 Okt 2026 · 16.30 WIB</span>
                    </div>

                    <div id="modal-timeline-decision" class="flex items-center gap-2.5">
                        <span id="modal-timeline-decision-dot" class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: #F5A623;"></span>
                        <span id="modal-timeline-decision-text" class="text-amber-700 font-medium">Menunggu persetujuan Admin SDM</span>
                    </div>
                </div>
            </div>

            <!-- Form Keputusan Approval / Rejection -->
            <form id="modal-decision-form" method="POST" action="" class="space-y-4 pt-1">
                @csrf
                <div>
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <label for="modal-notes" class="text-[13px] font-bold text-slate-900">
                            Catatan keputusan
                        </label>
                        <span class="text-[12px] text-slate-400 font-normal">
                            (opsional jika disetujui)
                        </span>
                    </div>
                    <textarea 
                        name="notes" 
                        id="modal-notes" 
                        rows="3" 
                        placeholder="Tambahkan catatan untuk karyawan..." 
                        class="w-full border border-slate-200/90 rounded-xl p-3 text-[13px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors bg-white resize-y"
                    ></textarea>
                    <p class="text-[11.5px] text-slate-400 mt-1">
                        Alasan wajib diisi jika pengajuan ditolak.
                    </p>
                </div>

                <div id="modal-action-buttons" class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        name="status" 
                        value="ditolak" 
                        class="w-full sm:w-1/2 py-2.5 px-5 rounded-xl border border-red-200 bg-white hover:bg-red-50 text-red-600 font-semibold text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                    >
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Tolak</span>
                    </button>

                    <button 
                        type="submit" 
                        name="status" 
                        value="disetujui" 
                        class="w-full sm:w-1/2 py-2.5 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                        style="background-color: #4D7BFF !important; color: #ffffff !important;"
                    >
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Setujui pengajuan</span>
                    </button>
                </div>

                <p id="modal-footer-caption" class="text-[11.5px] text-slate-400 text-center block pt-0.5">
                    Karyawan akan menerima notifikasi setelah keputusan diberikan.
                </p>
            </form>

        </div>

    </div>
</div>
