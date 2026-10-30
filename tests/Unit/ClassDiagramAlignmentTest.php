<?php

namespace Tests\Unit;

use App\Models\Apel;
use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\EkuliahSyncLog;
use App\Models\LogSinkronisasi;
use App\Models\Mahasiswa;
use App\Models\Participant;
use App\Models\PengajuanIzin;
use App\Models\Pengguna;
use App\Models\PermissionRequest;
use App\Models\Presensi;
use App\Models\User;
use App\Services\EKuliahService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ClassDiagramAlignmentTest extends TestCase
{
    /**
     * Uji Entitas: Pengguna (User)
     */
    public function test_pengguna_attributes_and_operations(): void
    {
        $user = new User([
            'name' => 'Budi Santoso',
            'username' => 'budi123',
            'email' => 'budi@polinela.ac.id',
            'role' => 'timdis',
            'study_program' => 'Manajemen Informatika',
            'jurusan_binaan' => 'Teknologi Informasi',
            'avatar' => null,
        ]);
        $user->id = 15;

        // Atribut Pengguna
        $this->assertEquals(15, $user->id_pengguna);
        $this->assertEquals('budi123', $user->username);
        $this->assertEquals('Budi Santoso', $user->nama);
        $this->assertEquals('timdis', $user->peran);
        $this->assertNotEmpty($user->foto_profil);

        // Atribut Timdis
        $this->assertEquals(15, $user->id_timdis);
        $this->assertEquals('Manajemen Informatika', $user->prodi_binaan);
        $this->assertEquals('Teknologi Informasi', $user->jurusan_binaan);

        // Operasi Timdis eksporKehadiranExcel
        $excelFile = $user->eksporKehadiranExcel();
        $this->assertStringEndsWith('.xlsx', $excelFile);
    }

    /**
     * Uji Entitas: Mahasiswa (Participant)
     */
    public function test_mahasiswa_attributes_and_operations(): void
    {
        $participant = new Participant([
            'identifier' => '22753001',
            'name' => 'Ahmad Dahlan',
            'study_program' => 'Manajemen Informatika',
            'class_group' => 'MI 3A',
            'semester' => 3,
            'kode_qr' => 'QR_22753001_SAMPLE',
        ]);
        $participant->id = 99;

        // Atribut Mahasiswa (NPM Polinela & alias NIM)
        $this->assertEquals('22753001', $participant->npm);
        $this->assertEquals('22753001', $participant->nim);
        $this->assertEquals('MI 3A', $participant->kelas);
        $this->assertEquals(3, $participant->semester);
        $this->assertEquals('QR_22753001_SAMPLE', $participant->kode_qr);

        // Operasi Mahasiswa
        $this->assertEquals('QR_22753001_SAMPLE', $participant->unduhKartuQR());
        $this->assertEquals('BELUM_TERDAFTAR', $participant->cetakKartuRFID());
    }

    /**
     * Uji Entitas: Apel (ApelSchedule) & Presensi (AttendanceRecord)
     */
    public function test_apel_and_presensi_attributes(): void
    {
        $today = Carbon::today();
        $schedule = new ApelSchedule([
            'title' => 'Apel Pagi Gabungan',
            'category' => 'gabungan',
            'schedule_date' => $today,
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '07:30:00',
            'late_threshold_time' => '07:15:00',
            'status' => 'scheduled',
        ]);
        $schedule->id = 42;

        // Atribut Apel
        $this->assertEquals(42, $schedule->id_apel);
        $this->assertEquals($today->format('Y-m-d'), $schedule->tanggal->format('Y-m-d'));
        $this->assertEquals('07:00:00', $schedule->jam_mulai);
        $this->assertEquals('08:00:00', $schedule->jam_selesai);
        $this->assertEquals('gabungan', $schedule->jenis);
        $this->assertEquals('scheduled', $schedule->status);

        // Perhitungan keterlambatan
        $tapTimeTepat = Carbon::parse($today->format('Y-m-d').' 07:10:00');
        $tapTimeTerlambat = Carbon::parse($today->format('Y-m-d').' 07:25:00');
        $this->assertEquals('hadir', $schedule->determineStatus($tapTimeTepat));
        $this->assertEquals('terlambat', $schedule->determineStatus($tapTimeTerlambat));
        $this->assertEquals(0, $schedule->hitungMenitKeterlambatan($tapTimeTepat));
        $this->assertEquals(10, $schedule->hitungMenitKeterlambatan($tapTimeTerlambat));

        // Entitas Presensi
        $presensi = new AttendanceRecord([
            'apel_schedule_id' => 42,
            'participant_id' => 1,
            'check_in_time' => $tapTimeTerlambat,
            'status' => 'terlambat',
            'metode' => 'RFID',
            'menit_terlambat' => 10,
        ]);
        $presensi->id = 101;

        $this->assertEquals(101, $presensi->id_presensi);
        $this->assertEquals('RFID', $presensi->metode);
        $this->assertEquals('terlambat', $presensi->status);
        $this->assertEquals(10, $presensi->menit_terlambat);
        $this->assertEquals(10, $presensi->hitungKeterlambatan());
    }

    /**
     * Uji Entitas: PengajuanIzin (PermissionRequest)
     */
    public function test_pengajuan_izin_attributes_and_lifecycle(): void
    {
        $izin = new PermissionRequest([
            'participant_id' => 1,
            'apel_schedule_id' => 1,
            'type' => 'sakit',
            'reason' => 'Demam tinggi dan istirahat dokter',
            'attachment_path' => 'surat_dokter.pdf',
            'status' => 'menunggu',
            'alasan_penolakan' => null,
        ]);
        $izin->id = 55;

        // Atribut PengajuanIzin
        $this->assertEquals(55, $izin->id_izin);
        $this->assertEquals('sakit', $izin->jenis);
        $this->assertEquals('Demam tinggi dan istirahat dokter', $izin->alasan);
        $this->assertEquals('surat_dokter.pdf', $izin->berkas_bukti);
        $this->assertEquals('menunggu', $izin->status);
        $this->assertTrue($izin->isPending());
    }

    /**
     * Uji Entitas: LogSinkronisasi (EkuliahSyncLog)
     */
    public function test_log_sinkronisasi_attributes(): void
    {
        $log = new EkuliahSyncLog([
            'sync_type' => 'mahasiswa',
            'status' => 'success',
            'records_synced' => 150,
            'message' => 'Sinkronisasi berhasil',
            'synced_by' => 1,
        ]);
        $log->id = 77;
        $log->created_at = Carbon::parse('2026-10-28 08:00:00');

        // Atribut LogSinkronisasi
        $this->assertEquals(77, $log->id_sinkron);
        $this->assertEquals('2026-10-28 08:00:00', $log->waktu->format('Y-m-d H:i:s'));
        $this->assertEquals('success', $log->status);
        $this->assertEquals(150, $log->jumlah_data);
    }

    /**
     * Uji External Service: SistemEKuliah (EKuliahService)
     */
    public function test_ekuliah_service_operations(): void
    {
        Http::fake([
            '*' => Http::response(['data' => ['22753001', '22753002']], 200),
        ]);

        $service = new EKuliahService;

        // Operasi ambilDataMahasiswa() menghasilkan array
        $mhs = $service->ambilDataMahasiswa();
        $this->assertIsArray($mhs);
        $this->assertCount(2, $mhs);

        // Operasi terimaDataKehadiran() menghasilkan boolean true
        $pushResult = $service->terimaDataKehadiran([]);
        $this->assertTrue($pushResult);
    }

    /**
     * Uji Konsistensi Tabel Fisik & Model Alias Bahasa Indonesia sesuai Class Diagram
     */
    public function test_indonesian_table_and_model_aliases(): void
    {
        $this->assertEquals('pengguna', (new Pengguna)->getTable());
        $this->assertEquals('id_pengguna', (new Pengguna)->getKeyName());

        $this->assertEquals('mahasiswa', (new Mahasiswa)->getTable());
        $this->assertEquals('id_mahasiswa', (new Mahasiswa)->getKeyName());

        $this->assertEquals('apel', (new Apel)->getTable());
        $this->assertEquals('id_apel', (new Apel)->getKeyName());

        $this->assertEquals('presensi', (new Presensi)->getTable());
        $this->assertEquals('id_presensi', (new Presensi)->getKeyName());

        $this->assertEquals('pengajuan_izin', (new PengajuanIzin)->getTable());
        $this->assertEquals('id_izin', (new PengajuanIzin)->getKeyName());

        $this->assertEquals('log_sinkronisasi', (new LogSinkronisasi)->getTable());
        $this->assertEquals('id_sinkron', (new LogSinkronisasi)->getKeyName());

        // Verifikasi query pada tabel-tabel baru di database
        Pengguna::firstOrCreate(
            ['username' => 'test_cd_pengguna'],
            ['nama' => 'Test Pengguna', 'email' => 'test_cd@example.com', 'kata_sandi' => bcrypt('password'), 'peran' => 'petugas']
        );
        Mahasiswa::firstOrCreate(
            ['npm' => 'TEST_NPM_001'],
            ['nama' => 'Test Mahasiswa', 'kelas' => 'TRPL 1A']
        );
        $apel = Apel::firstOrCreate(
            ['tanggal' => Carbon::today()->toDateString(), 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:00:00'],
            [
                'title' => 'Apel Pagi CD Test',
                'jenis' => 'rutin',
                'attendance_start' => '06:30:00',
                'attendance_end' => '07:30:00',
                'late_threshold_time' => '07:00:00',
                'status' => 'scheduled',
            ]
        );
        Presensi::firstOrCreate(
            ['id_apel' => $apel->id_apel, 'id_mahasiswa' => Mahasiswa::where('npm', 'TEST_NPM_001')->first()->id_mahasiswa],
            ['status' => 'hadir', 'waktu_scan' => now()]
        );

        $this->assertGreaterThan(0, Pengguna::count());
        $this->assertGreaterThan(0, Mahasiswa::count());
        $this->assertGreaterThan(0, Apel::count());
        $this->assertGreaterThan(0, Presensi::count());
    }
}
