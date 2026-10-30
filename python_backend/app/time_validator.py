"""
Time Validation Module for SIAPEL Local QR Scanner Attendance System
Handles check-in and check-out time validation against Apel schedules
"""

import logging
from datetime import datetime, time as dt_time, timedelta
from typing import Dict
from pathlib import Path
from app.timezone_utils import get_current_time, convert_to_app_timezone, get_timezone_name

logger = logging.getLogger('TimeValidator')


class TimeValidator:
    """Validates attendance times against schedule rules"""

    def __init__(self, db_manager):
        self.db = db_manager
        self._grace_period_cache = None
        self._last_cache_time = None
        self._cache_version = None
        self._cache_version_file = Path('data/.schedule_cache_version')
        logger.info(f"TimeValidator initialized with timezone: {get_timezone_name()}")

    def _get_cache_version(self) -> int:
        try:
            if self._cache_version_file.exists():
                return int(self._cache_version_file.read_text().strip())
        except Exception as e:
            logger.warning(f"Failed to read cache version: {e}")
        return 0

    def _should_invalidate_cache(self) -> bool:
        current_version = self._get_cache_version()
        if self._cache_version is None or current_version != self._cache_version:
            self._cache_version = current_version
            return True
        return False

    def get_grace_period_minutes(self) -> int:
        """Get grace period with 5-minute cache"""
        now = get_current_time()

        if self._should_invalidate_cache():
            self._grace_period_cache = None
            self._last_cache_time = None

        if (
            self._grace_period_cache is None
            or self._last_cache_time is None
            or (now - self._last_cache_time).seconds > 300
        ):
            self._grace_period_cache = self.db.get_grace_period_minutes()
            self._last_cache_time = now
        return self._grace_period_cache

    def parse_time(self, time_value) -> dt_time:
        """Parse time from various formats to datetime.time object"""
        if isinstance(time_value, dt_time):
            return time_value
        elif isinstance(time_value, timedelta):
            total_seconds = int(time_value.total_seconds())
            hours = (total_seconds // 3600) % 24
            minutes = (total_seconds % 3600) // 60
            seconds = total_seconds % 60
            return dt_time(hours, minutes, seconds)
        elif isinstance(time_value, datetime):
            return time_value.time()
        elif isinstance(time_value, str):
            try:
                return datetime.strptime(time_value, '%H:%M:%S').time()
            except ValueError:
                return datetime.strptime(time_value, '%H:%M').time()
        else:
            raise ValueError(f"Cannot parse time from: {type(time_value)} - {time_value}")

    def calculate_minutes_difference(self, time1: dt_time, time2: dt_time) -> int:
        """Calculate difference in minutes between two times"""
        today = datetime.now().date()
        dt1 = datetime.combine(today, time1)
        dt2 = datetime.combine(today, time2)
        diff = (dt1 - dt2).total_seconds() / 60
        return max(1, int(round(diff)))

    def validate_check_in(self, current_time: datetime, schedule: Dict) -> Dict:
        """
        Validate check-in time against Apel schedule (timezone-aware)
        - check_in_start: waktu buka presensi (attendance_start)
        - check_in_end: batas tepat waktu (late_threshold_time)
        - attendance_end (atau check_in_end + grace_period): batas tutup presensi
        """
        if not schedule:
            return {
                'allowed': False,
                'is_late': False,
                'late_duration': 0,
                'reason': 'no_schedule',
                'message': 'Tidak ada jadwal apel aktif untuk hari ini'
            }

        try:
            current_time = convert_to_app_timezone(current_time)

            check_in_start = self.parse_time(schedule['check_in_start'])
            check_in_end = self.parse_time(schedule['check_in_end'])

            current_time_only = current_time.time()

            # Jika jadwal memiliki kolom attendance_end eksplisit, gunakan itu sebagai batas tutup
            if schedule.get('attendance_end'):
                grace_end_time = self.parse_time(schedule['attendance_end'])
            else:
                grace_period_minutes = self.get_grace_period_minutes()
                check_in_end_dt = datetime.combine(current_time.date(), check_in_end)
                check_in_end_dt = convert_to_app_timezone(check_in_end_dt.replace(tzinfo=current_time.tzinfo))
                grace_end_dt = check_in_end_dt + timedelta(minutes=grace_period_minutes)
                grace_end_time = grace_end_dt.time()

            # Terlalu awal (sebelum presensi dibuka)
            if current_time_only < check_in_start:
                return {
                    'allowed': False,
                    'is_late': False,
                    'late_duration': 0,
                    'reason': 'too_early',
                    'message': f'Presensi apel belum dibuka. Presensi dibuka pukul {check_in_start.strftime("%H:%M")}'
                }

            # Tepat waktu (check_in_start <= waktu_scan <= check_in_end / late_threshold)
            if check_in_start <= current_time_only <= check_in_end:
                return {
                    'allowed': True,
                    'is_late': False,
                    'late_duration': 0,
                    'reason': 'on_time',
                    'message': 'Presensi apel berhasil (Hadir Tepat Waktu)'
                }

            # Terlambat namun masih dalam periode presensi (check_in_end < waktu_scan <= grace_end_time)
            if check_in_end < current_time_only <= grace_end_time:
                late_minutes = self.calculate_minutes_difference(current_time_only, check_in_end)
                return {
                    'allowed': True,
                    'is_late': True,
                    'late_duration': late_minutes,
                    'reason': 'late',
                    'message': f'Presensi apel tercatat (TERLAMBAT {late_minutes} menit)'
                }

            # Melewati batas akhir penutupan presensi
            return {
                'allowed': False,
                'is_late': False,
                'late_duration': 0,
                'reason': 'too_late',
                'message': f'Presensi apel sudah ditutup. Batas akhir presensi pukul {grace_end_time.strftime("%H:%M")}'
            }

        except Exception as e:
            logger.error(f"Error validating check-in: {e}", exc_info=True)
            return {
                'allowed': False,
                'is_late': False,
                'late_duration': 0,
                'reason': 'error',
                'message': f'Error validasi waktu: {str(e)}'
            }

    def validate_check_out(self, current_time: datetime, schedule: Dict) -> Dict:
        """Validate check-out time against Apel schedule"""
        if not schedule:
            return {
                'allowed': False,
                'reason': 'no_schedule',
                'message': 'Tidak ada jadwal apel aktif untuk hari ini'
            }

        try:
            current_time = convert_to_app_timezone(current_time)
            check_out_start = self.parse_time(schedule.get('check_out_start') or '07:30:00')
            check_out_end = self.parse_time(schedule.get('check_out_end') or '23:59:59')
            current_time_only = current_time.time()

            if current_time_only < check_out_start:
                return {
                    'allowed': False,
                    'reason': 'too_early',
                    'message': f'Belum waktunya presensi keluar. Presensi keluar dibuka pukul {check_out_start.strftime("%H:%M")}'
                }

            if check_out_start <= current_time_only <= check_out_end:
                return {
                    'allowed': True,
                    'reason': 'valid',
                    'message': 'Presensi keluar berhasil dicatat'
                }

            return {
                'allowed': False,
                'reason': 'too_late',
                'message': f'Waktu presensi keluar sudah ditutup (batas pukul {check_out_end.strftime("%H:%M")})'
            }
        except Exception as e:
            logger.error(f"Error validating check-out: {e}", exc_info=True)
            return {
                'allowed': False,
                'reason': 'error',
                'message': f'Error validasi waktu: {str(e)}'
            }

