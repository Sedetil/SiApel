"""
Timezone Utility Module for Attendance System
Ensures consistent timezone handling across Python backend (Asia/Jakarta)
"""

import os
from datetime import datetime, time as dt_time
from zoneinfo import ZoneInfo
import logging

logger = logging.getLogger('TimezoneUtils')

DEFAULT_TIMEZONE = 'Asia/Jakarta'
APP_TIMEZONE = os.getenv('APP_TIMEZONE', DEFAULT_TIMEZONE)

try:
    TZ = ZoneInfo(APP_TIMEZONE)
except Exception as e:
    logger.warning(f"Failed to load timezone {APP_TIMEZONE}, falling back to {DEFAULT_TIMEZONE}: {e}")
    TZ = ZoneInfo(DEFAULT_TIMEZONE)
    APP_TIMEZONE = DEFAULT_TIMEZONE


def get_current_time() -> datetime:
    """Get current time in application timezone (timezone-aware)"""
    return datetime.now(TZ)


def get_current_date():
    """Get current date in application timezone"""
    return get_current_time().date()


def get_current_time_only() -> dt_time:
    """Get current time (time component only) in application timezone"""
    return get_current_time().time()


def make_aware(dt: datetime) -> datetime:
    """Convert naive datetime to timezone-aware datetime"""
    if dt.tzinfo is None:
        return dt.replace(tzinfo=TZ)
    return dt


def convert_to_app_timezone(dt: datetime) -> datetime:
    """Convert datetime from any timezone to application timezone"""
    if dt.tzinfo is None:
        return dt.replace(tzinfo=TZ)
    return dt.astimezone(TZ)


def format_datetime(dt: datetime, format_str: str = '%Y-%m-%d %H:%M:%S') -> str:
    """Format datetime to string in application timezone"""
    aware_dt = convert_to_app_timezone(dt)
    return aware_dt.strftime(format_str)


def format_time(t: dt_time, format_str: str = '%H:%M:%S') -> str:
    """Format time to string"""
    return t.strftime(format_str)


def get_timezone_name() -> str:
    """Get the application timezone name"""
    return APP_TIMEZONE


def now():
    """Alias for get_current_time()"""
    return get_current_time()


def today():
    """Alias for get_current_date()"""
    return get_current_date()

