<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Apel Kedisiplinan — POLINELA</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #0F1729;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0F1729;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 4px 0 0 0;
        }
        .header p {
            font-size: 9pt;
            color: #6B7A90;
            margin: 4px 0 0 0;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 16px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .stats-box {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            border: 1px solid #D9D9D9;
            border-radius: 6px;
            padding: 10px;
            background: #F4F6FC;
            font-size: 10pt;
        }
        .stats-item {
            flex: 1;
            text-align: center;
        }
        .stats-item strong {
            display: block;
            font-size: 14pt;
            font-family: monospace;
            color: #2D56FF;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 24px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #D9D9D9;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #F4F6FC;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .badge {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            display: inline-block;
        }
        .badge-hadir { background: #dcfce7; color: #15803d; }
        .badge-terlambat { background: #fef3c7; color: #b45309; }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 220px;
            text-align: center;
            font-size: 10pt;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #0F1729;
            font-weight: bold;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
        }
        .print-btn {
            background: #2D56FF;
            color: white;
            padding: 8px 16px;
            font-size: 10pt;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Cetak Dokumen / Simpan PDF</button>
    </div>

    <!-- Institutional Letterhead -->
    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h1>
        <h2>POLITEKNIK NEGERI LAMPUNG</h2>
        <p>Jl. Soekarno Hatta No. 10 Rajabasa, Bandar Lampung 35144 &bull; Telp. (0721) 703995 &bull; Website: www.polinela.ac.id</p>
    </div>

    <div class="doc-title">
        LAPORAN REKAPITULASI PRESENSI APEL KEDISIPLINAN
    </div>

    <!-- Metadata -->
    <table class="meta-table">
        <tr>
            <td style="width: 15%;">Waktu Cetak</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ date('d F Y, H:i:s') }} WIB</td>
            <td style="width: 15%;">Operator Cetak</td>
            <td style="width: 2%;">:</td>
            <td style="width: 18%;">{{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
        <tr>
            <td>Sistem</td>
            <td>:</td>
            <td>SIABSEN POLINELA (RFID Attendance Engine)</td>
            <td>Total Baris</td>
            <td>:</td>
            <td>{{ $records->count() }} Data</td>
        </tr>
    </table>

    <!-- Table of Attendance Records -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">NPM</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 20%;">Program Studi / Kelas</th>
                <th style="width: 15%;">Sesi Apel</th>
                <th style="width: 10%;" class="text-center">Jam Tap</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $record->participant?->identifier }}</td>
                    <td><strong>{{ $record->participant?->name }}</strong></td>
                    <td>
                        {{ $record->participant?->study_program ?? '-' }}
                        <div style="font-size: 8pt; color: #6B7A90;">{{ $record->participant?->class_group ?? '-' }}</div>
                    </td>
                    <td>
                        {{ $record->schedule?->title }}
                        <div style="font-size: 8pt; color: #6B7A90;">{{ $record->schedule?->schedule_date->format('d/m/Y') }}</div>
                    </td>
                    <td class="text-center font-mono">
                        {{ $record->check_in_time ? $record->check_in_time->format('H:i:s') : '-' }}
                    </td>
                    <td class="text-center">
                        @if($record->status === 'hadir')
                            <span class="badge badge-hadir">HADIR</span>
                        @else
                            <span class="badge badge-terlambat">TERLAMBAT</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #6B7A90;">
                        Tidak ada data catatan presensi untuk kriteria laporan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Blocks -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Timdis Pelaksana Apel,</p>
            <div class="signature-line">
                {{ (auth()->user()->isTimdis() || auth()->user()->isPetugas()) ? auth()->user()->name : '( ............................................... )' }}
            </div>
            <p style="margin-top: 4px; font-size: 9pt; color: #6B7A90;">NIP/NPM Timdis</p>
        </div>

        <div class="signature-box">
            <p>Bandar Lampung, {{ date('d F Y') }}<br>Pembina Apel Kedisiplinan,</p>
            <div class="signature-line">
                {{ auth()->user()->isAdmin() ? auth()->user()->name : '( ............................................... )' }}
            </div>
            <p style="margin-top: 4px; font-size: 9pt; color: #6B7A90;">NIP Pembina</p>
        </div>
    </div>

</body>
</html>
