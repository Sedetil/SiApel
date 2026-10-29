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

class WebMonitoringSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $timdis;

    protected ApelSchedule $schedule;

    protected Participant $participant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Penguji Monitoring',
            'username' => 'admin_test_sec',
            'email' => 'admin_sec@polinela.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->timdis = User::create([
            'name' => 'Tim Disiplin Penguji',
            'username' => 'timdis_test_sec',
            'email' => 'timdis_sec@polinela.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'timdis',
            'is_active' => true,
        ]);

        $this->schedule = ApelSchedule::create([
            'title' => 'Apel Sesi Uji Monitoring',
            'schedule_date' => Carbon::today(),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '08:00:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan Utama',
            'status' => 'active',
        ]);

        $this->participant = Participant::create([
            'identifier' => 'NPM_SEC_8801',
            'name' => 'Mahasiswa Uji Monitoring',
            'study_program' => 'Manajemen Informatika',
            'class_group' => 'Kompi A',
            'is_active' => true,
        ]);

        RfidCard::create([
            'participant_id' => $this->participant->id,
            'card_uid' => 'UID_SEC_8801',
            'is_active' => true,
            'issued_at' => now(),
        ]);

        AttendanceRecord::create([
            'apel_schedule_id' => $this->schedule->id,
            'participant_id' => $this->participant->id,
            'check_in_time' => Carbon::now()->subMinutes(10),
            'status' => 'hadir',
        ]);
    }

    /**
     * Uji 1: Admin dan Timdis dapat mengakses seluruh halaman monitoring pada prefix masing-masing
     */
    public function test_admin_and_timdis_can_access_monitoring_pages(): void
    {
        // Admin
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/monitoring')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/presensi/riwayat')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/reports')->assertStatus(200);

        // Timdis
        $this->actingAs($this->timdis)->get('/timdis/dashboard')->assertStatus(200);
        $this->actingAs($this->timdis)->get('/timdis/monitoring')->assertStatus(200);
        $this->actingAs($this->timdis)->get('/timdis/presensi/riwayat')->assertStatus(200);
        $this->actingAs($this->timdis)->get('/timdis/reports')->assertStatus(200);
    }

    /**
     * Uji 2: Otorisasi lintas peran ditolak (Admin dilarang akses rute khusus timdis, dan sebaliknya)
     */
    public function test_cross_role_access_is_forbidden(): void
    {
        // Timdis mencoba akses dashboard dan monitoring admin -> 403 Forbidden
        $this->actingAs($this->timdis)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($this->timdis)->get('/admin/monitoring')->assertStatus(403);

        // Admin mencoba akses rute khusus timdis -> 403 Forbidden
        $this->actingAs($this->admin)->get('/timdis/dashboard')->assertStatus(403);
        $this->actingAs($this->admin)->get('/timdis/monitoring')->assertStatus(403);
    }

    /**
     * Uji 3: Timdis ditolak (403 Forbidden) mengakses modul konfigurasi Admin via URL langsung
     */
    public function test_timdis_cannot_access_admin_management_modules(): void
    {
        // CRUD Master Peserta
        $this->actingAs($this->timdis)->get('/admin/participants')->assertStatus(403);
        $this->actingAs($this->timdis)->get('/admin/participants/create')->assertStatus(403);

        // CRUD Jadwal
        $this->actingAs($this->timdis)->get('/admin/schedules')->assertStatus(403);
        $this->actingAs($this->timdis)->get('/admin/schedules/create')->assertStatus(403);

        // Kelola Petugas / Timdis
        $this->actingAs($this->timdis)->get('/admin/officers')->assertStatus(403);

        // Audit Log
        $this->actingAs($this->timdis)->get('/admin/audit-logs')->assertStatus(403);
    }

    /**
     * Uji 4: Admin dan Timdis dapat melihat profil detail peserta dalam mode baca pada prefix masing-masing
     */
    public function test_admin_and_timdis_can_view_participant_detail_read_only(): void
    {
        // Admin dapat melihat
        $resAdmin = $this->actingAs($this->admin)->get("/admin/participants/{$this->participant->id}");
        $resAdmin->assertStatus(200)
            ->assertSee($this->participant->name)
            ->assertSee($this->participant->identifier)
            ->assertSee('Detail Peserta (Mode Baca)');

        // Timdis dapat melihat
        $resTimdis = $this->actingAs($this->timdis)->get("/timdis/participants/{$this->participant->id}");
        $resTimdis->assertStatus(200)
            ->assertSee($this->participant->name)
            ->assertSee($this->participant->identifier)
            ->assertSee('Detail Peserta (Mode Baca)');
    }

    /**
     * Uji 5: Timdis ditolak saat mencoba memutasi data peserta
     */
    public function test_timdis_cannot_mutate_participant_data(): void
    {
        // Coba edit form
        $this->actingAs($this->timdis)->get("/admin/participants/{$this->participant->id}/edit")->assertStatus(403);

        // Coba submit update
        $this->actingAs($this->timdis)->put("/admin/participants/{$this->participant->id}", [
            'name' => 'Nama Diubah',
            'identifier' => 'NPM_SEC_8801',
            'is_active' => 1,
        ])->assertStatus(403);

        // Coba petakan RFID
        $this->actingAs($this->timdis)->post("/admin/participants/{$this->participant->id}/map-rfid", [
            'card_uid' => 'UID_HACK_999',
        ])->assertStatus(403);

        // Coba toggle status
        $this->actingAs($this->timdis)->post("/admin/participants/{$this->participant->id}/toggle-status")->assertStatus(403);
    }

    /**
     * Uji 6: Pengguna tanpa login dialihkan ke halaman login
     */
    public function test_unauthenticated_users_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin/monitoring')->assertRedirect('/login');
        $this->get('/timdis/dashboard')->assertRedirect('/login');
        $this->get('/timdis/monitoring')->assertRedirect('/login');
        $this->get("/admin/participants/{$this->participant->id}")->assertRedirect('/login');
        $this->get("/timdis/participants/{$this->participant->id}")->assertRedirect('/login');
    }

    /**
     * Uji 7: Akses root / mengalihkan sesuai role
     */
    public function test_authenticated_user_accessing_root_redirects_to_role_dashboard(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->actingAs($this->admin)->get('/')->assertRedirect('/admin/dashboard');
        $this->actingAs($this->timdis)->get('/')->assertRedirect('/timdis/dashboard');
    }

    /**
     * Uji 8: Web tidak lagi memiliki endpoint mutasi presensi langsung (Strictly Read-Only Web)
     */
    public function test_web_cannot_mutate_attendance_records(): void
    {
        // Endpoint lama /presensi/scan-process tidak lagi diizinkan via web
        $response = $this->actingAs($this->admin)->post('/presensi/scan-process', [
            'apel_schedule_id' => $this->schedule->id,
            'card_uid' => 'UID_SEC_8801',
        ]);

        // Harus 404 Not Found karena rute telah dihapus dari web
        $this->assertTrue(in_array($response->status(), [404, 405]));
    }

    /**
     * Uji 9: Endpoint polling live feed monitoring bersifat read-only dan mengembalikan format JSON yang valid
     */
    public function test_live_feed_endpoint_returns_json_and_is_read_only(): void
    {
        $response = $this->actingAs($this->timdis)->getJson("/timdis/monitoring/feed?schedule_id={$this->schedule->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'stats' => ['total_participants', 'total_hadir', 'total_terlambat', 'total_recorded'],
                'recent_records',
                'timestamp',
            ]);
    }

    /**
     * Uji 10: Filter dan pencarian pada riwayat presensi berfungsi normal
     */
    public function test_filter_and_search_in_attendance_history(): void
    {
        // Cari berdasarkan nama
        $resName = $this->actingAs($this->timdis)->get('/timdis/presensi/riwayat?search=Mahasiswa+Uji');
        $resName->assertStatus(200)->assertSee('Mahasiswa Uji Monitoring');

        // Cari berdasarkan status
        $resStatus = $this->actingAs($this->timdis)->get('/timdis/presensi/riwayat?status=hadir');
        $resStatus->assertStatus(200)->assertSee('Mahasiswa Uji Monitoring');

        // Cari dengan kriteria yang tidak ada
        $resNone = $this->actingAs($this->timdis)->get('/timdis/presensi/riwayat?search=NON_EXISTENT_PARTICIPANT_XYZ999');
        $resNone->assertStatus(200)->assertSee('Tidak Ada Data Presensi');
    }
}
