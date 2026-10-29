@props(['officers'])

<!-- 4. TABEL DATA TIMDIS -->
<div class="border border-slate-100 rounded-xl overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[880px]">
        
        <!-- Header Tabel -->
        <thead class="bg-white border-b border-slate-100 text-[12px] font-medium text-slate-400 select-none">
            <tr>
                <th class="w-11 py-3 px-3 text-center">
                    <input 
                        type="checkbox" 
                        id="selectAllOfficersCheckbox" 
                        onchange="toggleSelectAllOfficers(this)"
                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer transition-colors"
                    >
                </th>
                <th class="py-3 px-4 font-medium text-slate-400">Anggota timdis</th>
                <th class="py-3 px-4 font-medium text-slate-400">Username</th>
                <th class="py-3 px-4 font-medium text-slate-400">Nomor telepon</th>
                <th class="py-3 px-4 font-medium text-slate-400">Peran & penugasan</th>
                <th class="py-3 px-4 font-medium text-slate-400">Status akun</th>
                <th class="py-3 px-4 font-medium text-slate-400 text-center">Aksi</th>
            </tr>
        </thead>

        <!-- Body Tabel (10 Baris Mock Data) -->
        <tbody id="officersTableBody" class="divide-y divide-slate-100/90 text-[13px]">
            @foreach($officers as $officer)
                @include('pages.officers.table-row', ['officer' => $officer])
            @endforeach
        </tbody>
    </table>

    <!-- State Kosong Jika Filter Tidak Menemukan Hasil -->
    <div id="noOfficerResults" class="hidden p-10 text-center text-slate-400 text-[13px]">
        <i data-lucide="search-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
        <p class="font-bold text-slate-700 text-[14px]">Anggota timdis tidak ditemukan</p>
        <p class="text-[12px] text-slate-400 mt-0.5">Tidak ada data yang sesuai dengan filter pencarian.</p>
    </div>
</div>
