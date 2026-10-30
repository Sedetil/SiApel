<!-- INTERACTIVE DETAIL & APPROVAL MODAL -->
<div id="detail-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-[15px] font-bold text-slate-900 leading-tight">Detail Pengajuan Izin/Sakit</h3>
                    <p class="text-[11px] text-slate-500">Verifikasi alasan dan keputusan Timdis</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4 text-[13px]">
            
            <!-- Identitas Mahasiswa -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                <div>
                    <div id="modal-student-name" class="font-bold text-slate-900 text-[14px]">-</div>
                    <div id="modal-student-npm" class="font-mono text-[11px] text-slate-500">-</div>
                </div>
                <span id="modal-type-badge" class="px-2.5 py-1 rounded text-[11px] font-bold">-</span>
            </div>

            <!-- Sesi Apel & Alasan -->
            <div class="space-y-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Sesi Apel Kedisiplinan:</span>
                    <p id="modal-schedule" class="text-slate-800 font-medium">-</p>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Alasan Pengajuan:</span>
                    <p id="modal-reason" class="text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-200/60 leading-relaxed">-</p>
                </div>

                <!-- Bukti Lampiran -->
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Bukti Surat / Dokumen:</span>
                    <div id="modal-attachment-container" class="p-3 rounded-lg border border-slate-200 bg-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                            <span id="modal-attachment-name" class="font-medium text-slate-800 truncate max-w-[240px]">-</span>
                        </div>
                        <span class="text-[11px] text-blue-600 font-semibold cursor-pointer hover:underline">Pratinjau Berkas</span>
                    </div>
                </div>

                <!-- Keputusan Timdis -->
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Keputusan Timdis:</span>
                    <div id="modal-decision-box" class="p-3 rounded-lg border text-[12px] space-y-1">
                        <div class="flex items-center justify-between">
                            <strong id="modal-decision-status" class="capitalize">-</strong>
                            <span id="modal-decision-date" class="text-slate-400 font-mono text-[11px]">-</span>
                        </div>
                        <p id="modal-decision-notes" class="text-slate-600 italic">-</p>
                        <p id="modal-decision-officer" class="text-slate-500 text-[11px] mt-1 font-medium">-</p>
                    </div>
                </div>
            </div>

            <!-- Action Form for Approval / Rejection (If Pending) -->
            <form id="modal-decision-form" method="POST" action="" class="pt-3 border-t border-slate-100 space-y-3">
                @csrf
                <div id="decision-inputs" class="space-y-2">
                    <label for="notes" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Catatan Keputusan (Opsional):
                    </label>
                    <input type="text" name="notes" id="notes" placeholder="Contoh: Bukti surat dokter valid dan terverifikasi."
                           class="w-full text-[12px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-600">
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="submit" name="status" value="ditolak" class="px-4 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold text-[12.5px] transition-colors cursor-pointer">
                        Tolak Pengajuan
                    </button>
                    <button type="submit" name="status" value="disetujui" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[12.5px] transition-colors shadow-sm cursor-pointer">
                        Setujui Izin/Sakit
                    </button>
                </div>
            </form>

        </div>

    </div>
</div>
