@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-[1360px] mx-auto">

    <!-- 2. Judul Halaman -->
    <div>
        <h1 class="text-[26px] font-bold text-slate-900 tracking-tight">
            Riwayat Absensi
        </h1>
    </div>

    <!-- 3. Empat Kartu Ringkasan / Summary Cards (Grid 1x4) -->
    @include('pages.attendance.history.summary-cards')

    <!-- 4. Area Pencarian & Filter (Card Putih, Lebar Penuh) -->
    @include('pages.attendance.history.filter-section')

    <!-- 5. Tabel Data Absensi (Card Putih, Lebar Penuh) -->
    @include('pages.attendance.history.table-section')

    <!-- 6. Pagination (Bawah Tengah) -->
    @include('pages.attendance.history.pagination')

</div>
@endsection
