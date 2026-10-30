<?php

namespace Tests\Feature;

use App\Models\ApelSchedule;
use App\Models\Participant;
use App\Models\RfidCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $officer;

    protected ApelSchedule $schedule;

    protected Participant $participant;

    protected RfidCard $card;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::create([
            'name' => 'Petugas Lapangan Uji',
            'username' => 'petugas_test_api',
            'email' => 'petugas_api@polinela.ac.id',
            'password' => Hash::make('secret123'),
            'role' => 'petugas',
            'is_active' => true,
        ]);

        $this->schedule = ApelSchedule::create([
            'title' => 'Apel Sesi Uji API',
            'schedule_date' => Carbon::today(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'attendance_start' => '00:00:00',
            'attendance_end' => '23:59:59',
            'late_threshold_time' => '12:00:00',
            'location' => 'Lapangan POLINELA',
            'status' => 'active',
        ]);

        $this->participant = Participant::create([
            'identifier' => 'NPM_API_9901',
            'name' => 'Budi Mahasiswa Uji',
            'study_program' => 'Teknologi Informasi',
            'is_active' => true,
        ]);

        $this->card = RfidCard::create([
            'participant_id' => $this->participant->id,
            'card_uid' => 'UID_API_111',
            'is_active' => true,
            'issued_at' => now(),
        ]);
    }

    /**
     * AC-01: Login dengan kredensial valid menghasilkan token Sanctum
     */
    public function test_officer_login_successful(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'petugas_test_api',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'username' => 'petugas_test_api',
                    'role' => 'petugas',
                ],
            ])
            ->assertJsonStructure(['token', 'user']);
    }

    /**
     * AC-01: Login dengan kata sandi salah ditolak dengan pesan aman
     */
    public function test_login_invalid_password_rejected(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'petugas_test_api',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * AC-01: Akun nonaktif ditolak
     */
    public function test_inactive_officer_cannot_login(): void
    {
        $this->officer->update(['is_active' => false]);

        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'petugas_test_api',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 'ACCOUNT_INACTIVE',
            ]);
    }

    /**
     * AC-08: Endpoint terproteksi menolak permintaan tanpa token autentikasi
     */
    public function test_unauthenticated_request_rejected(): void
    {
        $response = $this->getJson('/api/v1/schedules/active');
        $response->assertStatus(401);
    }

    /**
     * AC-04: Scan RFID via API menghasilkan status 200 dan data presensi
     */
    public function test_scan_rfid_endpoint_success(): void
    {
        $token = $this->officer->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/attendance/scan', [
                'apel_schedule_id' => $this->schedule->id,
                'card_uid' => 'UID_API_111',
                'scan_type' => 'check_in',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 'CHECK_IN_SUCCESS',
                'participant' => [
                    'identifier' => 'NPM_API_9901',
                    'name' => 'Budi Mahasiswa Uji',
                ],
            ]);
    }

    /**
     * AC-04: Scan kartu tidak terdaftar via API menghasilkan status 404
     */
    public function test_scan_unknown_card_returns_404(): void
    {
        $token = $this->officer->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/attendance/scan', [
                'apel_schedule_id' => $this->schedule->id,
                'card_uid' => 'UNKNOWN_RANDOM_UID',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'code' => 'CARD_NOT_REGISTERED',
            ]);
    }

    /**
     * AC-04: Scan duplikat via API menghasilkan status 409
     */
    public function test_scan_duplicate_card_returns_409(): void
    {
        $token = $this->officer->createToken('test')->plainTextToken;

        // Scan pertama
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/attendance/scan', [
                'apel_schedule_id' => $this->schedule->id,
                'card_uid' => 'UID_API_111',
            ]);

        // Scan kedua (duplikat)
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/attendance/scan', [
                'apel_schedule_id' => $this->schedule->id,
                'card_uid' => 'UID_API_111',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'code' => 'DUPLICATE_SCAN',
            ]);
    }

    /**
     * FR-08 & FR-11: Schedule summary endpoint returns correct statistics
     */
    public function test_schedule_summary_endpoint(): void
    {
        $token = $this->officer->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/schedules/{$this->schedule->id}/summary");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'schedule' => [
                    'id' => $this->schedule->id,
                ],
            ])
            ->assertJsonStructure(['statistics' => ['total_participants', 'total_recorded', 'total_hadir', 'total_terlambat']]);
    }
}
