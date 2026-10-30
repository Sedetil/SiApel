import mysql.connector
from datetime import datetime
import logging
import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent))

from app.config_db import MYSQL_CONFIG
from app.timezone_utils import get_current_time, get_current_date, format_datetime

logger = logging.getLogger('DatabaseManager')


class DatabaseManager:
    def __init__(self):
        logger.info(f"Menggunakan MySQL Lokal: {MYSQL_CONFIG['host']}:{MYSQL_CONFIG['port']}/{MYSQL_CONFIG['database']}")
        self._ensure_database_exists()
        self._init_db()

    def _ensure_database_exists(self):
        """Pastikan database MySQL lokal sudah ada sebelum koneksi tabel"""
        try:
            temp_cfg = dict(MYSQL_CONFIG)
            db_name = temp_cfg.pop('database')
            conn = mysql.connector.connect(**temp_cfg)
            cursor = conn.cursor()
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS `{db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")
            conn.commit()
            cursor.close()
            conn.close()
        except Exception as e:
            logger.warning(f"Tidak dapat membuat database otomatis (mungkin sudah ada): {e}")

    def _get_conn(self):
        """Dapatkan koneksi MySQL"""
        return mysql.connector.connect(**MYSQL_CONFIG)

    def _execute(self, query, params=None, fetch_one=False, fetch_all=False):
        """Execute query MySQL menggunakan parameterized query"""
        conn = self._get_conn()
        cursor = conn.cursor()

        try:
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)

            result = None
            if fetch_one:
                row = cursor.fetchone()
                if row:
                    columns = [desc[0] for desc in cursor.description]
                    result = dict(zip(columns, row))
            elif fetch_all:
                rows = cursor.fetchall()
                columns = [desc[0] for desc in cursor.description]
                result = [dict(zip(columns, row)) for row in rows]
            else:
                conn.commit()
                result = cursor.lastrowid

            return result
        except mysql.connector.Error as e:
            logger.error(f"Database error: {e}")
            conn.rollback()
            raise
        finally:
            cursor.close()
            conn.close()

    def _init_db(self):
        """Inisialisasi tabel MySQL lokal untuk sistem presensi QR Scanner Python"""
        conn = self._get_conn()
        cursor = conn.cursor()

        cursor.execute("""
            CREATE TABLE IF NOT EXISTS mahasiswa (
                id VARCHAR(64) PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                kompi VARCHAR(100) NOT NULL DEFAULT '-',
                jurusan VARCHAR(100) NOT NULL DEFAULT '-',
                prodi VARCHAR(100) DEFAULT '-',
                email VARCHAR(255),
                no_telp_mahasiswa VARCHAR(30),
                no_telp_ortu VARCHAR(30),
                qr_code_id VARCHAR(100) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_active TINYINT(1) DEFAULT 1,
                INDEX idx_qr_code (qr_code_id),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        """)

        cursor.execute("""
            CREATE TABLE IF NOT EXISTS pkkmb_schedules (
                id BIGINT UNSIGNED PRIMARY KEY,
                hari_ke INT DEFAULT 1,
                title VARCHAR(255) DEFAULT 'Apel Mahasiswa',
                tanggal DATE NOT NULL,
                check_in_start TIME NOT NULL,
                check_in_end TIME NOT NULL,
                attendance_end TIME NULL,
                check_out_start TIME NOT NULL,
                check_out_end TIME NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                INDEX idx_tanggal (tanggal),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        """)

        try:
            cursor.execute("ALTER TABLE pkkmb_schedules ADD COLUMN title VARCHAR(255) DEFAULT 'Apel Mahasiswa'")
        except mysql.connector.Error:
            pass

        try:
            cursor.execute("ALTER TABLE pkkmb_schedules ADD COLUMN attendance_end TIME NULL")
        except mysql.connector.Error:
            pass

        cursor.execute("""
            CREATE TABLE IF NOT EXISTS kegiatan (
                id BIGINT UNSIGNED PRIMARY KEY,
                nama VARCHAR(255) NOT NULL,
                tanggal_pelaksanaan DATE NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                INDEX idx_tanggal_kegiatan (tanggal_pelaksanaan),
                INDEX idx_active_kegiatan (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        """)

        cursor.execute("""
            CREATE TABLE IF NOT EXISTS attendance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                mahasiswa_id VARCHAR(64) NOT NULL,
                check_in DATETIME,
                check_out DATETIME,
                check_in_time TIME,
                check_out_time TIME,
                date DATE NOT NULL,
                status VARCHAR(20) DEFAULT 'hadir',
                kegiatan_id BIGINT UNSIGNED NULL,
                is_late TINYINT(1) DEFAULT 0,
                late_duration INT DEFAULT 0,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_date (date),
                INDEX idx_mahasiswa (mahasiswa_id),
                INDEX idx_checkin (check_in)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        """)

        cursor.execute("""
            CREATE TABLE IF NOT EXISTS system_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                config_key VARCHAR(255) UNIQUE NOT NULL,
                config_value TEXT,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_key (config_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        """)

        cursor.execute("""
            INSERT IGNORE INTO system_config (config_key, config_value, description)
            VALUES ('attendance_grace_period_minutes', '45', 'Grace period keterlambatan absensi apel dalam menit')
        """)

        conn.commit()
        cursor.close()
        conn.close()
        logger.info("Tabel MySQL lokal untuk QR Scanner berhasil diinisialisasi.")

    def get_mahasiswa_by_qr(self, qr_code_id: str):
        """
        Cari peserta berdasarkan kode QR dari Alat Scanner QR.
        Mendukung pencarian berdasarkan qr_code_id, NPM (id), atau format QR-<NPM>.
        Jika belum disinkron ke tabel `mahasiswa`, fallback otomatis ke tabel `participants` Laravel jika satu database.
        """
        if not qr_code_id:
            return None

        raw = str(qr_code_id).strip()
        clean = re.sub(r'^(QR-|-)', '', raw, flags=re.IGNORECASE).strip()

        # 1. Cari di tabel lokal `mahasiswa` hasil Sync Master Data
        row = self._execute(
            "SELECT * FROM mahasiswa WHERE qr_code_id = %s OR id = %s OR qr_code_id = %s OR id = %s LIMIT 1",
            (raw, raw, clean, clean),
            fetch_one=True
        )
        if row:
            return row

        # 2. Fallback ke tabel `participants` & `rfid_cards` jika menggunakan database lokal yang sama dengan SiApel
        try:
            fallback = self._execute("""
                SELECT
                    p.identifier AS id,
                    p.name,
                    COALESCE(p.class_group, '-') AS kompi,
                    COALESCE(p.study_program, '-') AS jurusan,
                    COALESCE(p.study_program, '-') AS prodi,
                    COALESCE(rc.card_uid, p.identifier) AS qr_code_id,
                    p.is_active
                FROM participants p
                LEFT JOIN rfid_cards rc ON rc.participant_id = p.id AND rc.is_active = 1
                WHERE p.deleted_at IS NULL
                  AND (p.identifier = %s OR p.identifier = %s OR rc.card_uid = %s OR rc.card_uid = %s)
                LIMIT 1
            """, (raw, clean, raw.upper(), clean.upper()), fetch_one=True)
            return fallback
        except Exception:
            return None

    def get_active_kegiatan(self):
        """Ambil daftar jadwal/sesi apel yang sedang aktif"""
        try:
            rows = self._execute(
                "SELECT id, nama, tanggal_pelaksanaan FROM kegiatan WHERE is_active = 1 ORDER BY tanggal_pelaksanaan DESC",
                fetch_all=True
            )
            if rows:
                return rows
        except Exception as e:
            logger.warning(f"Query kegiatan lokal gagal: {e}")

        # Fallback ke tabel `apel_schedules` jika satu database dengan Laravel
        try:
            return self._execute("""
                SELECT id, title AS nama, schedule_date AS tanggal_pelaksanaan
                FROM apel_schedules
                WHERE status IN ('active', 'scheduled')
                ORDER BY schedule_date DESC
            """, fetch_all=True)
        except Exception:
            return []

    def get_schedule_for_date(self, date_str=None):
        """Ambil jadwal apel untuk tanggal tertentu (default: hari ini)"""
        if date_str is None:
            date_str = get_current_date().strftime('%Y-%m-%d')

        # 1. Cek tabel `pkkmb_schedules` hasil Sync Master Data
        row = self._execute("""
            SELECT id, hari_ke, title, tanggal, check_in_start, check_in_end,
                   attendance_end, check_out_start, check_out_end, is_active
            FROM pkkmb_schedules
            WHERE tanggal = %s AND is_active = 1
            LIMIT 1
        """, (date_str,), fetch_one=True)
        if row:
            return row

        # 2. Fallback ke tabel `apel_schedules` jika satu database dengan Laravel SiApel
        try:
            return self._execute("""
                SELECT
                    id,
                    1 AS hari_ke,
                    title,
                    schedule_date AS tanggal,
                    attendance_start AS check_in_start,
                    late_threshold_time AS check_in_end,
                    attendance_end,
                    end_time AS check_out_start,
                    '23:59:59' AS check_out_end,
                    1 AS is_active
                FROM apel_schedules
                WHERE (schedule_date = %s AND status IN ('active', 'scheduled'))
                   OR status = 'active'
                ORDER BY CASE WHEN status = 'active' THEN 0 ELSE 1 END, schedule_date DESC
                LIMIT 1
            """, (date_str,), fetch_one=True)
        except Exception:
            return None

    def get_today_schedule(self):
        """Ambil jadwal apel hari ini"""
        return self.get_schedule_for_date()

    def get_all_schedules(self):
        """Ambil semua jadwal apel di database lokal"""
        rows = self._execute("""
            SELECT id, hari_ke, title, tanggal, check_in_start, check_in_end,
                   attendance_end, check_out_start, check_out_end, is_active
            FROM pkkmb_schedules
            ORDER BY tanggal DESC
        """, fetch_all=True)
        if rows:
            return rows

        try:
            return self._execute("""
                SELECT
                    id,
                    1 AS hari_ke,
                    title,
                    schedule_date AS tanggal,
                    attendance_start AS check_in_start,
                    late_threshold_time AS check_in_end,
                    attendance_end,
                    end_time AS check_out_start,
                    '23:59:59' AS check_out_end,
                    CASE WHEN status IN ('active', 'scheduled') THEN 1 ELSE 0 END AS is_active
                FROM apel_schedules
                ORDER BY schedule_date DESC
            """, fetch_all=True)
        except Exception:
            return []

    def get_system_config(self, config_key: str, default_value=None):
        """Ambil konfigurasi sistem berdasarkan key"""
        query = "SELECT config_value FROM system_config WHERE config_key = %s"
        result = self._execute(query, (config_key,), fetch_one=True)
        return result['config_value'] if result else default_value

    def get_grace_period_minutes(self) -> int:
        """Ambil grace period dalam menit dari system_config"""
        value = self.get_system_config('attendance_grace_period_minutes', '45')
        try:
            return int(value)
        except (ValueError, TypeError):
            return 45

