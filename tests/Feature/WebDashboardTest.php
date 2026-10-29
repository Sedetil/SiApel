<?php

namespace Tests\Feature;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\RfidCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $officer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Utama Uji',
            'username' => 'admin_test_web',
            'email' => 'admin_web@polinela.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->officer = User::create([
            'name' => 'Petugas Apel Uji',
            'username' => 'petugas_test_web',
            'email' => 'petugas_web@polinela.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'is_active' => true,
        ]);
    }

    /**
     * AC-01: Admin dapat login ke web dashboard
     */
    public function test_admin_can_login_via_web(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'admin_test_web',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_officer_can_login_via_web_and_redirects_to_timdis_dashboard(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'petugas_test_web',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/timdis/dashboard');
        $this->assertAuthenticatedAs($this->officer);
    }

    /**
     * AC-01: Petugas tidak dapat mengakses menu administrasi (403 Forbidden)
     */
    public function test_officer_cannot_access_admin_modules(): void
    {
        $response = $this->actingAs($this->officer)->get('/admin/participants');
        $response->assertStatus(403);

        $resSched = $this->actingAs($this->officer)->get('/admin/schedules');
        $resSched->assertStatus(403);

        $resOfficers = $this->actingAs($this->officer)->get('/admin/officers');
        $resOfficers->assertStatus(403);
    }

    /**
     * AC-02: Admin dapat menambah data peserta baru
     */
    public function test_admin_can_create_participant(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/participants', [
            'identifier' => '22753099_WEB',
            'name' => 'Mahasiswa Baru Web',
            'study_program' => 'Manajemen Informatika',
            'class_group' => 'Kompi C',
        ]);

        $response->assertRedirect('/admin/participants');
        $this->assertDatabaseHas('mahasiswa', [
            'npm' => '22753099_WEB',
            'nama' => 'Mahasiswa Baru Web',
        ]);
    }

    /**
     * AC-02: Sistem menolak nomor induk peserta duplikat
     */
    public function test_duplicate_participant_identifier_rejected(): void
    {
        Participant::create([
            'identifier' => '22753099_DUP',
            'name' => 'Mahasiswa Lama',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/participants', [
            'identifier' => '22753099_DUP',
            'name' => 'Mahasiswa Kembar',
        ]);

        $response->assertSessionHasErrors(['identifier']);
    }

    /**
     * AC-02: Pemetaan kartu RFID ke peserta
     */
    public function test_admin_can_map_rfid_card(): void
    {
        $participant = Participant::create([
            'identifier' => '22753088_MAP',
            'name' => 'Peserta Kartu Map',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/participants/{$participant->id}/map-rfid", [
            'card_uid' => 'UID_BARU_888_MAP',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('rfid_cards', [
            'id_mahasiswa' => $participant->id,
            'card_uid' => 'UID_BARU_888_MAP',
            'is_active' => true,
        ]);
    }

    /**
     * AC-02: Sistem menolak pemetaan kartu aktif yang sudah digunakan peserta lain
     */
    public function test_cannot_map_card_already_active_on_another_participant(): void
    {
        $p1 = Participant::create(['identifier' => 'NPM001_SH', 'name' => 'Peserta 1']);
        $p2 = Participant::create(['identifier' => 'NPM002_SH', 'name' => 'Peserta 2']);

        // P1 memegang kartu UID_SHARED
        RfidCard::create([
            'participant_id' => $p1->id,
            'card_uid' => 'UID_SHARED_99',
            'is_active' => true,
        ]);

        // Coba petakan kartu yang sama ke P2
        $response = $this->actingAs($this->admin)->post("/admin/participants/{$p2->id}/map-rfid", [
            'card_uid' => 'UID_SHARED_99',
        ]);

        $response->assertSessionHasErrors(['card_uid']);
    }

    /**
     * FR-04: Jadwal yang memiliki catatan absensi tidak boleh dihapus
     */
    public function test_cannot_delete_schedule_with_attendance_records(): void
    {
        $schedule = ApelSchedule::create([
            'title' => 'Apel Ada Absensi Uji',
            'schedule_date' => Carbon::today(),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '08:00:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan',
            'status' => 'active',
        ]);

        $participant = Participant::create(['identifier' => 'NPM003_DEL', 'name' => 'Peserta 3']);

        AttendanceRecord::create([
            'apel_schedule_id' => $schedule->id,
            'participant_id' => $participant->id,
            'check_in_time' => now(),
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/schedules/{$schedule->id}");

        $response->assertSessionHasErrors(['schedule']);
        $this->assertDatabaseHas('apel', ['id_apel' => $schedule->id]);
    }

    /**
     * AC-06: Ekspor laporan format CSV
     */
    public function test_admin_can_export_csv(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/export-csv');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    /**
     * AC-06: Buka format cetak PDF
     */
    public function test_admin_can_view_pdf_report(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/export-pdf');
        $response->assertStatus(200);
        $response->assertSee('POLITEKNIK NEGERI LAMPUNG');
    }

    /**
     * Verifikasi metrik Hadir Tepat Waktu, Tidak Hadir, dan Distribusi Kehadiran memakai Real Data
     */
    public function test_dashboard_displays_real_data_for_attendance_stats_and_distribution(): void
    {
        $schedule = ApelSchedule::create([
            'title' => 'Apel Pagi Pengujian Real Data',
            'schedule_date' => Carbon::today(),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '08:00:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan POLINELA',
            'status' => 'active',
        ]);

        $p1 = Participant::create(['identifier' => 'REAL_01', 'name' => 'Peserta Hadir 1', 'is_active' => true]);
        $p2 = Participant::create(['identifier' => 'REAL_02', 'name' => 'Peserta Hadir 2', 'is_active' => true]);
        $p3 = Participant::create(['identifier' => 'REAL_03', 'name' => 'Peserta Terlambat', 'is_active' => true]);
        $p4 = Participant::create(['identifier' => 'REAL_04', 'name' => 'Peserta Izin', 'is_active' => true]);

        AttendanceRecord::create([
            'apel_schedule_id' => $schedule->id,
            'participant_id' => $p1->id,
            'check_in_time' => Carbon::now()->subMinutes(15),
            'status' => 'hadir',
        ]);

        AttendanceRecord::create([
            'apel_schedule_id' => $schedule->id,
            'participant_id' => $p2->id,
            'check_in_time' => Carbon::now()->subMinutes(10),
            'status' => 'hadir',
        ]);

        AttendanceRecord::create([
            'apel_schedule_id' => $schedule->id,
            'participant_id' => $p3->id,
            'check_in_time' => Carbon::now()->subMinutes(5),
            'status' => 'terlambat',
        ]);

        AttendanceRecord::create([
            'apel_schedule_id' => $schedule->id,
            'participant_id' => $p4->id,
            'check_in_time' => null,
            'status' => 'izin',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/dashboard?schedule_id={$schedule->id}");

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['session_hadir'] === 2 &&
                   $stats['session_terlambat'] === 1 &&
                   $stats['session_izin'] === 1 &&
                   $stats['session_total'] === 3;
        });

        // Verifikasi tidak ada fallback dummy seperti 3118 atau 2326 dari 248 orang
        $response->assertDontSee('3118');
        $response->assertDontSee('2326 dari 248 orang');
    }

    /**
     * Verifikasi tab tren kehadiran 7, 30, dan 90 hari berfungsi
     */
    public function test_dashboard_attendance_trend_tabs_function_for_7_30_and_90_days(): void
    {
        // 1. Tab 7 hari
        $res7 = $this->actingAs($this->admin)->get('/admin/dashboard?trend_days=7');
        $res7->assertStatus(200);
        $res7->assertViewHas('trendDays', 7);
        $res7->assertSee('vs 7 hari sebelumnya');
        $res7->assertViewHas('trendData', function ($data) {
            return $data['days'] === 7 && count($data['points']) === 7;
        });

        // 2. Tab 30 hari
        $res30 = $this->actingAs($this->admin)->get('/admin/dashboard?trend_days=30');
        $res30->assertStatus(200);
        $res30->assertViewHas('trendDays', 30);
        $res30->assertSee('vs 30 hari sebelumnya');
        $res30->assertViewHas('trendData', function ($data) {
            return $data['days'] === 30 && count($data['points']) === 30;
        });

        // 3. Tab 90 hari
        $res90 = $this->actingAs($this->admin)->get('/admin/dashboard?trend_days=90');
        $res90->assertStatus(200);
        $res90->assertViewHas('trendDays', 90);
        $res90->assertSee('vs 90 hari sebelumnya');
        $res90->assertViewHas('trendData', function ($data) {
            return $data['days'] === 90 && count($data['points']) === 90;
        });
    }
}
