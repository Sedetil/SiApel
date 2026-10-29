<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * FR-09: Halaman Rekap dan Laporan
     */
    public function index(Request $request): View
    {
        $selectedScheduleId = $request->input('schedule_id');
        $selectedStatus = $request->input('status');
        $selectedProdi = $request->input('study_program');
        $selectedDate = $request->input('date');

        $query = AttendanceRecord::with(['participant', 'schedule', 'recorder']);

        if ($selectedScheduleId) {
            $query->where('apel_schedule_id', $selectedScheduleId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        if ($selectedProdi) {
            $query->whereHas('participant', function ($q) use ($selectedProdi) {
                $q->where('study_program', $selectedProdi);
            });
        }

        if ($selectedDate) {
            $query->whereHas('schedule', function ($q) use ($selectedDate) {
                $q->whereDate('schedule_date', $selectedDate);
            });
        }

        $records = $query->orderBy('check_in_time', 'desc')->paginate(20)->withQueryString();

        // Statistik ringkasan sesuai filter
        $stats = [
            'total_recorded' => (clone $query)->count(),
            'total_hadir' => (clone $query)->where('status', 'hadir')->count(),
            'total_terlambat' => (clone $query)->where('status', 'terlambat')->count(),
        ];

        $schedules = ApelSchedule::orderBy('schedule_date', 'desc')->get();
        $studyPrograms = Participant::select('study_program')
            ->whereNotNull('study_program')
            ->distinct()
            ->pluck('study_program');

        return view('pages.reports.index', compact(
            'records',
            'stats',
            'schedules',
            'studyPrograms',
            'selectedScheduleId',
            'selectedStatus',
            'selectedProdi',
            'selectedDate'
        ));
    }

    /**
     * FR-10: Ekspor Laporan ke Format CSV / Excel
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request);
        $records = $query->get();

        $filename = 'Laporan_Absensi_Apel_'.Carbon::now()->format('Ymd_His').'.csv';

        AuditLog::log(
            action: 'EXPORT_REPORT_CSV',
            description: 'Mengekspor laporan absensi format CSV ('.$records->count().' baris).',
            userId: Auth::id()
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header Dokumen
            fputcsv($handle, ['REKAPITULASI PRESENSI APEL KEDISIPLINAN POLINELA']);
            fputcsv($handle, ['Waktu Ekspor:', Carbon::now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, ['Operator:', Auth::user()?->name ?? 'System']);
            fputcsv($handle, []);

            // Header Tabel
            fputcsv($handle, [
                'No',
                'NPM',
                'Nama Peserta',
                'Program Studi',
                'Kompi / Pleton',
                'Jadwal Apel',
                'Tanggal Apel',
                'Waktu Masuk',
                'Waktu Keluar',
                'Metode Scan',
                'Menit Terlambat',
                'Status Kehadiran',
                'Timdis Pencatat',
            ]);

            $no = 1;
            foreach ($records as $record) {
                fputcsv($handle, [
                    $no++,
                    $record->participant?->npm ?? $record->participant?->identifier ?? '-',
                    $record->participant?->name ?? '-',
                    $record->participant?->study_program ?? '-',
                    $record->participant?->class_group ?? '-',
                    $record->schedule?->title ?? '-',
                    $record->schedule?->schedule_date?->format('d/m/Y') ?? '-',
                    $record->check_in_time ? $record->check_in_time->format('H:i:s') : '-',
                    $record->check_out_time ? $record->check_out_time->format('H:i:s') : '-',
                    $record->metode ?? 'RFID',
                    $record->hitungKeterlambatan().' menit',
                    strtoupper($record->status),
                    $record->recorder?->name ?? 'Sistem RFID',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * FR-10: Ekspor Laporan ke Format Cetak / PDF Print View
     */
    public function exportPdf(Request $request): View
    {
        $query = $this->buildFilteredQuery($request);
        $records = $query->get();

        $stats = [
            'total_peserta' => Participant::active()->count(),
            'total_recorded' => $records->count(),
            'total_hadir' => $records->where('status', 'hadir')->count(),
            'total_terlambat' => $records->where('status', 'terlambat')->count(),
        ];

        AuditLog::log(
            action: 'EXPORT_REPORT_PDF',
            description: 'Membuka format cetak laporan absensi PDF ('.$records->count().' baris).',
            userId: Auth::id()
        );

        return view('pages.reports.pdf', compact('records', 'stats'));
    }

    protected function buildFilteredQuery(Request $request)
    {
        $query = AttendanceRecord::with(['participant', 'schedule', 'recorder']);

        if ($request->filled('schedule_id')) {
            $query->where('apel_schedule_id', $request->input('schedule_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('study_program')) {
            $prodi = $request->input('study_program');
            $query->whereHas('participant', function ($q) use ($prodi) {
                $q->where('study_program', $prodi);
            });
        }

        if ($request->filled('class_group')) {
            $classGroup = $request->input('class_group');
            $query->whereHas('participant', function ($q) use ($classGroup) {
                $q->where('class_group', $classGroup);
            });
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $query->whereHas('schedule', function ($q) use ($date) {
                $q->whereDate('schedule_date', $date);
            });
        }

        return $query->orderBy('check_in_time', 'asc');
    }
}
