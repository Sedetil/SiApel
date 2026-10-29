<?php

namespace Tests\Unit;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\RfidCard;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected AttendanceService $service;

    protected ApelSchedule $schedule;

    protected Participant $participant;

    protected RfidCard $card;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AttendanceService;

        // Buat jadwal apel untuk pengujian
        $this->schedule = ApelSchedule::create([
            'title' => 'Apel Pagi Pengujian Unit',
            'schedule_date' => Carbon::parse('2026-10-22'),
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '08:00:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan Apel Unit Test',
            'status' => 'active',
        ]);

        // Buat peserta aktif
        $this->participant = Participant::create([
            'identifier' => 'TEST_UNIT_001',
            'name' => 'Peserta Uji Unit',
            'study_program' => 'Informatika',
            'class_group' => 'Kompi A',
            'is_active' => true,
        ]);

        // Petakan kartu RFID
        $this->card = RfidCard::create([
            'participant_id' => $this->participant->id,
            'card_uid' => 'CARD_TEST_UNIT_123',
            'is_active' => true,
            'issued_at' => now(),
        ]);
    }

    /**
     * AC-03 & AC-04: Pemindaian sebelum atau pada batas waktu menghasilkan status HADIR
     */
    public function test_scan_on_time_produces_hadir_status(): void
    {
        $tapTime = Carbon::parse('2026-10-22 07:10:00');

        $result = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            deviceIdentifier: 'test-device',
            customServerTime: $tapTime
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('CHECK_IN_SUCCESS', $result['code']);
        $this->assertEquals('hadir', $result['attendance']['status']);

        $this->assertDatabaseHas('presensi', [
            'id_apel' => $this->schedule->id,
            'id_mahasiswa' => $this->participant->id,
            'status' => 'hadir',
        ]);
    }

    /**
     * AC-03: Pemindaian tepat pada batas waktu menghasilkan status HADIR
     */
    public function test_scan_exactly_at_late_threshold_produces_hadir_status(): void
    {
        $tapTime = Carbon::parse('2026-10-22 07:15:00');

        $result = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            customServerTime: $tapTime
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('hadir', $result['attendance']['status']);
    }

    /**
     * AC-03: Pemindaian setelah batas waktu menghasilkan status TERLAMBAT
     */
    public function test_scan_after_threshold_produces_terlambat_status(): void
    {
        $tapTime = Carbon::parse('2026-10-22 07:15:01');

        $result = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            customServerTime: $tapTime
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('CHECK_IN_SUCCESS', $result['code']);
        $this->assertEquals('terlambat', $result['attendance']['status']);

        $this->assertDatabaseHas('presensi', [
            'id_apel' => $this->schedule->id,
            'id_mahasiswa' => $this->participant->id,
            'status' => 'terlambat',
        ]);
    }

    /**
     * AC-04: Pemindaian kartu yang sama berulang kali ditolak (anti-duplikasi idempoten)
     */
    public function test_duplicate_scan_is_rejected(): void
    {
        $tapTime1 = Carbon::parse('2026-10-22 07:05:00');
        $tapTime2 = Carbon::parse('2026-10-22 07:06:00');

        // Scan pertama
        $res1 = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            customServerTime: $tapTime1
        );
        $this->assertTrue($res1['success']);

        // Scan kedua (duplikat)
        $res2 = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            customServerTime: $tapTime2
        );
        $this->assertFalse($res2['success']);
        $this->assertEquals('DUPLICATE_SCAN', $res2['code']);

        // Pastikan hanya ada 1 catatan di database
        $this->assertEquals(1, AttendanceRecord::where('apel_schedule_id', $this->schedule->id)
            ->where('participant_id', $this->participant->id)
            ->count());
    }

    /**
     * AC-04: Kartu tidak terdaftar ditolak
     */
    public function test_unregistered_card_is_rejected(): void
    {
        $tapTime = Carbon::parse('2026-10-22 07:05:00');

        $result = $this->service->processScan(
            cardUid: 'UNKNOWN_UID_999',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            customServerTime: $tapTime
        );

        $this->assertFalse($result['success']);
        $this->assertEquals('CARD_NOT_REGISTERED', $result['code']);
    }

    /**
     * AC-04: Peserta nonaktif ditolak
     */
    public function test_inactive_participant_is_rejected(): void
    {
        $this->participant->update(['is_active' => false]);
        $tapTime = Carbon::parse('2026-10-22 07:05:00');

        $result = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            scanType: 'check_in',
            customServerTime: $tapTime
        );

        $this->assertFalse($result['success']);
        $this->assertEquals('PARTICIPANT_INACTIVE', $result['code']);
    }

    /**
     * AC-03: Pemindaian di luar jam periode absensi ditolak
     */
    public function test_scan_out_of_attendance_window_is_rejected(): void
    {
        // Sebelum jam buka presensi (06:30)
        $tapTimeBefore = Carbon::parse('2026-10-22 06:15:00');
        $resBefore = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            customServerTime: $tapTimeBefore
        );
        $this->assertFalse($resBefore['success']);
        $this->assertEquals('OUT_OF_ATTENDANCE_PERIOD', $resBefore['code']);

        // Setelah jam tutup presensi (08:00)
        $tapTimeAfter = Carbon::parse('2026-10-22 08:15:00');
        $resAfter = $this->service->processScan(
            cardUid: 'CARD_TEST_UNIT_123',
            scheduleId: $this->schedule->id,
            customServerTime: $tapTimeAfter
        );
        $this->assertFalse($resAfter['success']);
        $this->assertEquals('OUT_OF_ATTENDANCE_PERIOD', $resAfter['code']);
    }

    /**
     * AC-05: Absensi keluar (check-out)
     */
    public function test_check_out_records_checkout_time(): void
    {
        $inTime = Carbon::parse('2026-10-22 07:05:00');
        $outTime = Carbon::parse('2026-10-22 07:55:00');

        // Check-in
        $this->service->processScan('CARD_TEST_UNIT_123', $this->schedule->id, 'check_in', null, null, $inTime);

        // Check-out
        $resOut = $this->service->processScan('CARD_TEST_UNIT_123', $this->schedule->id, 'check_out', null, null, $outTime);

        $this->assertTrue($resOut['success']);
        $this->assertEquals('CHECK_OUT_SUCCESS', $resOut['code']);

        $record = AttendanceRecord::where('apel_schedule_id', $this->schedule->id)
            ->where('participant_id', $this->participant->id)
            ->first();

        $this->assertNotNull($record->check_out_time);
        $this->assertEquals('07:55:00', $record->check_out_time->format('H:i:s'));
    }

    /**
     * AC-05: Absensi keluar tanpa check-in ditolak
     */
    public function test_check_out_without_check_in_is_rejected(): void
    {
        $outTime = Carbon::parse('2026-10-22 07:55:00');

        $result = $this->service->processScan('CARD_TEST_UNIT_123', $this->schedule->id, 'check_out', null, null, $outTime);

        $this->assertFalse($result['success']);
        $this->assertEquals('NO_CHECK_IN_RECORD', $result['code']);
    }
}
