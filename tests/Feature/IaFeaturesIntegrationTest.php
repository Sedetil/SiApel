<?php

namespace Tests\Feature;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\PermissionRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IaFeaturesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $timdis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Utama',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->timdis = User::create([
            'name' => 'Petugas Timdis',
            'username' => 'timdis_test',
            'email' => 'timdis@test.com',
            'role' => 'timdis',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_permission_requests_page_and_filter()
    {
        $participant = Participant::create([
            'identifier' => '24572010',
            'name' => 'Ahmad Santoso',
            'study_program' => 'Manajemen Informatika',
            'class_group' => 'MI-3A',
        ]);

        PermissionRequest::create([
            'participant_id' => $participant->id,
            'type' => 'sakit',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'reason' => 'Demam tinggi dan istirahat dokter',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->get('/izin-sakit');
        $response->assertStatus(200);
        $response->assertSee('Demam tinggi dan istirahat dokter');

        // Filter tab menunggu
        $responseFiltered = $this->actingAs($this->admin)->get('/izin-sakit?status=menunggu');
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee($participant->name);
    }

    public function test_timdis_can_decide_permission_request()
    {
        $participant = Participant::create([
            'identifier' => '24572011',
            'name' => 'Budi Pratama',
            'study_program' => 'Teknik Komputer',
            'class_group' => 'TK-1A',
        ]);

        $schedule = ApelSchedule::create([
            'title' => 'Apel Rutin Pagi',
            'category' => 'rutin',
            'schedule_date' => now()->toDateString(),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '07:30:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan POLINELA',
            'status' => 'scheduled',
        ]);

        $request = PermissionRequest::create([
            'participant_id' => $participant->id,
            'apel_schedule_id' => $schedule->id,
            'type' => 'izin',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'reason' => 'Mengikuti lomba olimpiade nasional',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->timdis)->post("/izin-sakit/{$request->id}/decide", [
            'status' => 'disetujui',
            'review_notes' => 'Disetujui untuk mewakili kampus',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permission_requests', [
            'id' => $request->id,
            'status' => 'disetujui',
            'decided_by' => $this->timdis->id,
        ]);

        // Verifikasi sinkronisasi otomatis ke AttendanceRecord
        $this->assertDatabaseHas('attendance_records', [
            'participant_id' => $participant->id,
            'apel_schedule_id' => $schedule->id,
            'status' => 'izin',
        ]);
    }

    public function test_ekuliah_integration_page_and_configuration()
    {
        $response = $this->actingAs($this->admin)->get('/integrasi-ekuliah');
        $response->assertStatus(200);
        $response->assertSee('Integrasi Sistem E-Kuliah');

        // Update Configuration
        $postResponse = $this->actingAs($this->admin)->post('/integrasi-ekuliah/config', [
            'ekuliah_url' => 'https://ekuliah-new.polinela.ac.id/api/v2',
            'ekuliah_api_key' => 'new_secret_key_12345',
            'ekuliah_auto_sync' => '1',
        ]);

        $postResponse->assertRedirect();
        $this->assertEquals('https://ekuliah-new.polinela.ac.id/api/v2', SystemSetting::get('ekuliah_url'));

        // Test manual sync action
        $syncResponse = $this->actingAs($this->admin)->post('/integrasi-ekuliah/sync-data');
        $syncResponse->assertRedirect();
        $this->assertDatabaseHas('ekuliah_sync_logs', [
            'sync_type' => 'mahasiswa',
            'status' => 'success',
        ]);
    }

    public function test_user_can_edit_profile_and_change_password()
    {
        $response = $this->actingAs($this->admin)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Edit Profil Akun');

        // Update Profile Info via PUT to /profile
        $updateProfile = $this->actingAs($this->admin)->put('/profile', [
            'name' => 'Administrator Baru',
            'username' => 'admin_baru',
            'email' => 'admin_baru@polinela.ac.id',
            'phone' => '081299998888',
            'study_program' => 'Manajemen Informatika',
        ]);

        $updateProfile->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Administrator Baru',
            'username' => 'admin_baru',
        ]);

        // Change Password via PUT
        $changePassword = $this->actingAs($this->admin)->put('/profile/password', [
            'current_password' => 'password123',
            'password' => 'newPassword_456',
            'password_confirmation' => 'newPassword_456',
        ]);

        $changePassword->assertRedirect();
        $this->assertTrue(Hash::check('newPassword_456', $this->admin->fresh()->password));
    }

    public function test_schedules_category_filtering_and_cancel_action()
    {
        $schedRutin = ApelSchedule::create([
            'title' => 'Apel Rutin Senin Pagi',
            'category' => 'rutin',
            'schedule_date' => now()->toDateString(),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '07:30:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan Utama',
            'status' => 'scheduled',
        ]);

        $schedKhusus = ApelSchedule::create([
            'title' => 'Apel Peringatan Dies Natalis',
            'category' => 'khusus',
            'schedule_date' => now()->toDateString(),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '07:30:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan Utama',
            'status' => 'scheduled',
        ]);

        // Filter category khusus
        $resFilter = $this->actingAs($this->admin)->get('/schedules?category=khusus');
        $resFilter->assertStatus(200);
        $resFilter->assertSee('Apel Peringatan Dies Natalis');
        $resFilter->assertDontSee('Apel Rutin Senin Pagi');

        // Cancel schedule
        $cancelRes = $this->actingAs($this->admin)->post("/schedules/{$schedRutin->id}/cancel", [
            'reason' => 'Cuaca hujan lebat di lapangan',
        ]);

        $cancelRes->assertRedirect();
        $this->assertEquals('cancelled', $schedRutin->fresh()->status);
        $this->assertStringContainsString('Cuaca hujan lebat', $schedRutin->fresh()->description);
    }

    public function test_attendance_history_with_date_and_class_filter_and_csv_export()
    {
        $schedule = ApelSchedule::create([
            'title' => 'Apel Pagi Mahasiswa',
            'category' => 'rutin',
            'schedule_date' => '2026-10-23',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '07:30:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan Utama',
            'status' => 'completed',
        ]);

        $participantA = Participant::create([
            'identifier' => 'NPM001',
            'name' => 'Mahasiswa Kelas A',
            'study_program' => 'Manajemen Informatika',
            'class_group' => 'MI-3A',
        ]);

        $participantB = Participant::create([
            'identifier' => 'NPM002',
            'name' => 'Mahasiswa Kelas B',
            'study_program' => 'Teknik Komputer',
            'class_group' => 'TRK-2B',
        ]);

        AttendanceRecord::create([
            'participant_id' => $participantA->id,
            'apel_schedule_id' => $schedule->id,
            'status' => 'hadir',
            'check_in_time' => '2026-10-23 06:45:00',
        ]);

        AttendanceRecord::create([
            'participant_id' => $participantB->id,
            'apel_schedule_id' => $schedule->id,
            'status' => 'hadir',
            'check_in_time' => '2026-10-23 06:50:00',
        ]);

        // Filter by class_group
        $resFilter = $this->actingAs($this->admin)->get('/presensi/riwayat?class_group=MI-3A');
        $resFilter->assertStatus(200);
        $resFilter->assertSee('Mahasiswa Kelas A');
        $resFilter->assertDontSee('Mahasiswa Kelas B');

        // Test CSV / Excel Export
        $exportRes = $this->actingAs($this->admin)->get('/reports/export-csv?class_group=MI-3A');
        $exportRes->assertStatus(200);
        $exportRes->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
