<?php

namespace Tests\Feature;

use App\Models\ApelSchedule;
use App\Models\Participant;
use App\Models\RfidCard;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PythonSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_master_data_for_python_local_sync(): void
    {
        $participant = Participant::create([
            'identifier' => '23753001',
            'name' => 'Dody Setiawan',
            'study_program' => 'Manajemen Informatika',
            'class_group' => 'Kompi A',
            'is_active' => true,
        ]);

        RfidCard::create([
            'participant_id' => $participant->id,
            'card_uid' => 'QR-23753001',
            'is_active' => true,
            'issued_at' => now(),
        ]);

        ApelSchedule::create([
            'title' => 'Apel Pagi Senin',
            'category' => 'pagi',
            'schedule_date' => Carbon::today()->format('Y-m-d'),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'late_threshold_time' => '07:00:00',
            'attendance_end' => '07:45:00',
            'location' => 'Lapangan Utama POLINELA',
            'status' => 'active',
        ]);

        $statusRes = $this->getJson('/api/sync/status');
        $statusRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('stats.mahasiswa_active_count', 1);

        $mhsRes = $this->getJson('/api/sync/mahasiswa');
        $mhsRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', '23753001')
            ->assertJsonPath('data.0.qr_code_id', 'QR-23753001');

        $schRes = $this->getJson('/api/sync/schedules');
        $schRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.title', 'Apel Pagi Senin');
    }

    public function test_can_push_local_qr_scanner_attendance_to_online_database(): void
    {
        $participant = Participant::create([
            'identifier' => '23753002',
            'name' => 'Budi Santoso',
            'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
            'class_group' => 'Kompi B',
            'is_active' => true,
        ]);

        $schedule = ApelSchedule::create([
            'title' => 'Apel Kedisiplinan',
            'category' => 'pagi',
            'schedule_date' => Carbon::today()->format('Y-m-d'),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:00:00',
            'late_threshold_time' => '07:00:00',
            'attendance_end' => '08:00:00',
            'location' => 'Lapangan POLINELA',
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/sync', [
            'data' => [
                [
                    'id' => 'local_1',
                    'mahasiswa_id' => '23753002',
                    'name' => 'Budi Santoso',
                    'kegiatan_id' => $schedule->id,
                    'check_in' => '06:50:00',
                    'check_out' => null,
                    'is_late' => false,
                    'late_duration' => 0,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('synced_count', 1)
            ->assertJsonPath('rejected_count', 0);

        $this->assertDatabaseHas('attendance_records', [
            'apel_schedule_id' => $schedule->id,
            'participant_id' => $participant->id,
            'status' => 'hadir',
            'device_identifier' => 'python-qr-local',
        ]);
    }
}

