"""
SIAPEL POLINELA — Python Local QR Scanner Backend
Server API lokal untuk pemindaian Alat Scanner QR, validasi jadwal lokal (MySQL),
backup Excel lokal, dan sinkronisasi dua arah dengan Server Laravel Online.
"""

import sys
import os
from pathlib import Path
import logging
from datetime import datetime, timedelta

sys.path.insert(0, str(Path(__file__).parent))

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(name)s: %(message)s'
)
logger = logging.getLogger(__name__)

from flask import Flask, jsonify, request, render_template, redirect
from flask_cors import CORS

from app.database_manager import DatabaseManager
from app.config_db import MYSQL_CONFIG, SCANNER_SETTINGS

app = Flask(__name__, static_folder='static', template_folder='templates')
CORS(app)

try:
    db = DatabaseManager()
    print("✓ Database MySQL Lokal berhasil terhubung")
except Exception as e:
    print(f"⚠ Koneksi Database MySQL Lokal gagal: {e}")
    print("⚠ Sistem berjalan dalam mode memori lokal (silakan periksa MySQL lokal Anda)")
    db = None

# In-memory attendance state (kegiatan_id/default -> {mahasiswa_id -> {'check_in': datetime, 'check_out': datetime}})
local_attendance_state = {}


def get_local_action(mahasiswa_id, kegiatan_id=None):
    """
    Tentukan aksi presensi (check_in, check_out, cooldown, already_checked_out, atau rejected)
    berdasarkan state lokal dan jadwal apel di database MySQL lokal.
    """
    cooldown_seconds = SCANNER_SETTINGS.get('qr_cooldown', 30)

    if kegiatan_id:
        key = str(kegiatan_id)
        if key not in local_attendance_state:
            local_attendance_state[key] = {}

        session = local_attendance_state[key]

        if mahasiswa_id not in session:
            # Cek apakah jadwal tersebut ada untuk menghitung keterlambatan
            is_late = False
            late_duration = 0
            if db:
                try:
                    sch = db._execute(
                        "SELECT * FROM pkkmb_schedules WHERE id = %s LIMIT 1",
                        (kegiatan_id,),
                        fetch_one=True
                    )
                    if sch:
                        from app.timezone_utils import get_current_time
                        from app.time_validator import TimeValidator
                        validator = TimeValidator(db)
                        val_res = validator.validate_check_in(get_current_time(), sch)
                        is_late = val_res.get('is_late', False)
                        late_duration = val_res.get('late_duration', 0)
                except Exception:
                    pass

            session[mahasiswa_id] = {
                'check_in': datetime.now(),
                'check_out': None,
                'is_late': is_late,
                'late_duration': late_duration
            }
            return ('check_in', is_late, late_duration)

        record = session[mahasiswa_id]
        if record['check_in'] and not record['check_out']:
            elapsed = (datetime.now() - record['check_in']).total_seconds()
            if elapsed < cooldown_seconds:
                return 'cooldown'
            record['check_out'] = datetime.now()
            return 'check_out'

        if record['check_in'] and record['check_out']:
            return 'already_checked_out'

        return ('check_in', False, 0)

    # Mode Harian / Jadwal Apel Hari Ini
    key = 'default'
    if key not in local_attendance_state:
        local_attendance_state[key] = {}

    session = local_attendance_state[key]

    if mahasiswa_id not in session:
        if db:
            schedule = db.get_today_schedule()
            if not schedule:
                return ('rejected', 'no_schedule', 'Tidak ada jadwal apel aktif untuk hari ini')

            from app.timezone_utils import get_current_time
            from app.time_validator import TimeValidator

            time_validator = TimeValidator(db)
            validation_result = time_validator.validate_check_in(get_current_time(), schedule)

            if not validation_result.get('allowed', False):
                reason = validation_result.get('reason', 'unknown')
                message = validation_result.get('message', 'Waktu presensi tidak valid')
                if reason == 'too_late':
                    return ('rejected', 'too_late', message)
                elif reason == 'too_early':
                    return ('rejected', 'too_early', message)
                return ('rejected', reason, message)

            is_late = validation_result.get('is_late', False)
            late_duration = validation_result.get('late_duration', 0)
        else:
            is_late = False
            late_duration = 0

        session[mahasiswa_id] = {
            'check_in': datetime.now(),
            'check_out': None,
            'is_late': is_late,
            'late_duration': late_duration
        }
        return ('check_in', is_late, late_duration)

    record = session[mahasiswa_id]

    if record['check_in'] and not record['check_out']:
        elapsed = (datetime.now() - record['check_in']).total_seconds()
        if elapsed < cooldown_seconds:
            return 'cooldown'

        if db:
            schedule = db.get_today_schedule()
            if not schedule:
                return ('rejected', 'no_schedule', 'Tidak ada jadwal apel aktif untuk hari ini')

            from app.timezone_utils import get_current_time
            from app.time_validator import TimeValidator

            time_validator = TimeValidator(db)
            validation_result = time_validator.validate_check_out(get_current_time(), schedule)

            if not validation_result.get('allowed', False):
                reason = validation_result.get('reason', 'unknown')
                message = validation_result.get('message', 'Waktu presensi keluar tidak valid')
                return ('rejected', reason, message)

        record['check_out'] = datetime.now()
        return 'check_out'

    if record['check_in'] and record['check_out']:
        return 'already_checked_out'

    return ('check_in', False, 0)


def reset_local_attendance(kegiatan_id=None):
    """Reset in-memory state saat Clear Data atau ganti sesi apel"""
    global local_attendance_state
    if kegiatan_id:
        local_attendance_state.pop(str(kegiatan_id), None)
    else:
        local_attendance_state = {}


@app.route('/', methods=['GET'])
def index_redirect():
    return redirect('/monitor')


@app.route('/monitor', methods=['GET'])
def monitor_view():
    """Halaman Utama Monitor Alat Scanner QR Lokal"""
    return render_template('monitor.html')


@app.route('/api/python/status', methods=['GET'])
def status():
    """Cek status server Python lokal & koneksi MySQL lokal"""
    return jsonify({
        'success': True,
        'status': 'running',
        'mode': 'qr_scanner_only',
        'database_connected': db is not None,
        'database_name': MYSQL_CONFIG.get('database'),
        'qr_cooldown': SCANNER_SETTINGS.get('qr_cooldown', 30)
    })


@app.route('/api/python/attendance', methods=['POST'])
def record_attendance():
    """
    Memproses hasil scan dari Alat Scanner QR.
    Mencari peserta di MySQL lokal & menentukan status (Hadir / Terlambat / Keluar).
    Data akan di-push ke Server Online saat tombol 'Sync ke Server' ditekan.
    """
    try:
        data = request.json or {}
        qr_code_id = str(data.get('mahasiswa_id', '')).strip()
        kegiatan_id = data.get('kegiatan_id') or None

        if not qr_code_id:
            return jsonify({
                'success': False,
                'message': 'Kode QR kosong'
            }), 400

        now = datetime.now()
        time_str = now.strftime('%H:%M:%S')

        if db is None:
            return jsonify({
                'success': True,
                'message': 'Disimpan di lokal (menunggu sync)',
                'result': {
                    'status': 'checked_in',
                    'time': time_str,
                    'is_late': False,
                    'late_duration': 0
                },
                'mahasiswa': {
                    'id': qr_code_id,
                    'name': f'Peserta ({qr_code_id})',
                    'kompi': 'Lokal',
                    'jurusan': '-'
                }
            })

        mahasiswa = db.get_mahasiswa_by_qr(qr_code_id)
        if not mahasiswa:
            return jsonify({
                'success': False,
                'message': f'Kode QR "{qr_code_id}" tidak terdaftar',
                'show_alert': True,
                'alert_type': 'error',
                'alert_title': 'QR Code Tidak Dikenali',
                'alert_text': f'Kode QR "{qr_code_id}" belum terdaftar di database lokal. Silakan klik "Sync Master Data" jika peserta baru ditambahkan di server.'
            }), 404

        if not mahasiswa.get('is_active'):
            return jsonify({
                'success': False,
                'message': f"Peserta '{mahasiswa.get('name')}' berstatus nonaktif.",
                'show_alert': True,
                'alert_type': 'warning',
                'alert_title': 'Peserta Nonaktif',
                'alert_text': f"{mahasiswa.get('name')} ({mahasiswa.get('id')}) tidak aktif. Silakan hubungi Administrator."
            }), 403

        actual_mahasiswa_id = mahasiswa['id']
        action = get_local_action(actual_mahasiswa_id, kegiatan_id)

        # Penolakan berdasarkan validasi jadwal
        if isinstance(action, tuple) and action[0] == 'rejected':
            _, reason, message = action

            alert_config = {
                'show_alert': True,
                'alert_type': 'error',
                'alert_title': 'Presensi Ditolak',
                'alert_text': message
            }

            if reason == 'no_schedule':
                alert_config.update({
                    'alert_type': 'warning',
                    'alert_title': 'Tidak Ada Jadwal Apel',
                    'alert_text': f'Tidak ada jadwal apel aktif untuk hari ini di database lokal.\n\nPastikan jadwal apel sudah disinkronkan atau pilih Sesi Apel pada dropdown.'
                })
            elif reason == 'too_early':
                alert_config.update({
                    'alert_title': 'Presensi Belum Dibuka',
                    'alert_text': f'{message}\n\n{mahasiswa.get("name")} belum dapat melakukan presensi.'
                })
            elif reason == 'too_late':
                alert_config.update({
                    'alert_title': 'Presensi Sudah Ditutup',
                    'alert_text': f'{message}\n\n{mahasiswa.get("name")} melewati batas waktu penutupan presensi.'
                })

            return jsonify({
                'success': False,
                'message': message,
                'reason': reason,
                'mahasiswa': {
                    'id': mahasiswa['id'],
                    'name': mahasiswa['name'],
                    'kompi': mahasiswa.get('kompi', '-'),
                    'jurusan': mahasiswa.get('jurusan', '-')
                },
                **alert_config
            }), 403

        # Check-in berhasil (Tepat Waktu atau Terlambat)
        if isinstance(action, tuple) and action[0] == 'check_in':
            _, is_late, late_duration = action

            response_data = {
                'success': True,
                'message': 'Presensi masuk tercatat di lokal',
                'result': {
                    'status': 'checked_in',
                    'time': time_str,
                    'is_late': is_late,
                    'late_duration': late_duration
                },
                'mahasiswa': {
                    'id': mahasiswa['id'],
                    'name': mahasiswa['name'],
                    'kompi': mahasiswa.get('kompi', '-'),
                    'jurusan': mahasiswa.get('jurusan', '-')
                }
            }

            if is_late:
                response_data.update({
                    'show_alert': True,
                    'alert_type': 'warning',
                    'alert_title': 'Terlambat Apel',
                    'alert_text': f'{mahasiswa["name"]} terlambat {late_duration} menit.\nPresensi masuk tetap tercatat dengan status TERLAMBAT.'
                })

            return jsonify(response_data)

        if action == 'cooldown':
            key = str(kegiatan_id) if kegiatan_id else 'default'
            remaining_sec = 0
            if key in local_attendance_state and actual_mahasiswa_id in local_attendance_state[key]:
                record = local_attendance_state[key][actual_mahasiswa_id]
                elapsed = (datetime.now() - record['check_in']).total_seconds()
                remaining = SCANNER_SETTINGS.get('qr_cooldown', 30) - elapsed
                remaining_sec = max(0, int(remaining))

            return jsonify({
                'success': True,
                'message': 'Pemindaian berulang diabaikan (masa jeda)',
                'result': {'status': 'cooldown'},
                'mahasiswa': {
                    'id': mahasiswa['id'],
                    'name': mahasiswa['name'],
                    'kompi': mahasiswa.get('kompi', '-'),
                    'jurusan': mahasiswa.get('jurusan', '-')
                },
                'show_alert': True,
                'alert_type': 'info',
                'alert_title': 'Sudah Tercatat Masuk',
                'alert_text': f'{mahasiswa["name"]} baru saja melakukan presensi masuk. Tunggu {remaining_sec} detik untuk presensi keluar.'
            })

        if action == 'already_checked_out':
            return jsonify({
                'success': True,
                'message': 'Peserta sudah menyelesaikan presensi masuk dan keluar',
                'result': {'status': 'already_checked_out'},
                'mahasiswa': {
                    'id': mahasiswa['id'],
                    'name': mahasiswa['name'],
                    'kompi': mahasiswa.get('kompi', '-'),
                    'jurusan': mahasiswa.get('jurusan', '-')
                },
                'show_alert': True,
                'alert_type': 'info',
                'alert_title': 'Presensi Sudah Lengkap',
                'alert_text': f'{mahasiswa["name"]} sudah menyelesaikan presensi masuk & keluar pada sesi ini.'
            })

        status_string = 'checked_out' if action == 'check_out' else 'checked_in'

        return jsonify({
            'success': True,
            'message': 'Presensi tercatat di lokal',
            'result': {
                'status': status_string,
                'time': time_str,
                'is_late': False,
                'late_duration': 0
            },
            'mahasiswa': {
                'id': mahasiswa['id'],
                'name': mahasiswa['name'],
                'kompi': mahasiswa.get('kompi', '-'),
                'jurusan': mahasiswa.get('jurusan', '-')
            }
        })
    except Exception as e:
        logger.error(f"Attendance error: {e}")
        return jsonify({
            'success': False,
            'message': str(e),
            'show_alert': True,
            'alert_type': 'error',
            'alert_title': 'Kesalahan Sistem Lokal',
            'alert_text': f'Terjadi kesalahan: {str(e)}'
        }), 500


@app.route('/api/python/reset-state', methods=['POST'])
def reset_attendance_state():
    """Reset in-memory attendance state"""
    try:
        data = request.json or {}
        kegiatan_id = data.get('kegiatan_id', None)
        reset_local_attendance(kegiatan_id)
        return jsonify({
            'success': True,
            'message': 'State presensi lokal berhasil di-reset'
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


@app.route('/api/python/backup', methods=['POST'])
def backup_to_excel():
    """Simpan backup data presensi lokal ke file Excel (.xlsx) sebelum push ke server"""
    try:
        data = (request.json or {}).get('data', [])
        if not data:
            return jsonify({'success': False, 'message': 'Tidak ada data untuk di-backup'}), 400

        import pandas as pd

        df = pd.DataFrame(data)
        backup_dir = os.path.join(str(Path(__file__).parent), 'backups')
        os.makedirs(backup_dir, exist_ok=True)

        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"absensi_apel_backup_{timestamp}.xlsx"
        filepath = os.path.join(backup_dir, filename)

        df.to_excel(filepath, index=False)

        return jsonify({
            'success': True,
            'message': f'Backup berhasil disimpan ke {filename}',
            'filename': filename
        })
    except Exception as e:
        logger.error(f"Backup failed: {e}")
        return jsonify({'success': False, 'message': f'Gagal backup Excel: {str(e)}'}), 500


@app.route('/api/python/backup', methods=['DELETE'])
def clear_backup_files():
    """Hapus seluruh file backup Excel lokal"""
    try:
        import glob

        backup_dir = os.path.join(str(Path(__file__).parent), 'backups')
        files = glob.glob(os.path.join(backup_dir, '*.xlsx'))

        deleted = 0
        for f in files:
            try:
                os.remove(f)
                deleted += 1
            except Exception as e:
                logger.error(f"Gagal menghapus file {f}: {e}")

        return jsonify({
            'success': True,
            'message': f'Berhasil menghapus {deleted} file backup lokal'
        })
    except Exception as e:
        return jsonify({'success': False, 'message': f'Gagal menghapus backup: {str(e)}'}), 500


@app.route('/api/python/kegiatan', methods=['GET'])
def get_kegiatan():
    """Ambil daftar jadwal/sesi apel aktif dari database MySQL lokal"""
    try:
        if db is None:
            return jsonify({'success': False, 'message': 'Database MySQL lokal belum terhubung'}), 500

        kegiatans = convert_timedelta_to_str(db.get_active_kegiatan())
        return jsonify({
            'success': True,
            'data': kegiatans
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


def convert_timedelta_to_str(obj):
    """Konversi objek timedelta/date ke string agar aman di-serialize ke JSON"""
    if obj is None:
        return None

    if isinstance(obj, timedelta):
        total_seconds = int(obj.total_seconds())
        hours = total_seconds // 3600
        minutes = (total_seconds % 3600) // 60
        seconds = total_seconds % 60
        return f"{hours:02d}:{minutes:02d}:{seconds:02d}"

    if hasattr(obj, 'isoformat'):
        return obj.isoformat()

    if isinstance(obj, dict):
        return {key: convert_timedelta_to_str(value) for key, value in obj.items()}

    if isinstance(obj, list):
        return [convert_timedelta_to_str(item) for item in obj]

    return obj


@app.route('/api/python/debug/schedule', methods=['GET'])
def debug_schedule():
    """Cek jadwal apel aktif hari ini di database lokal"""
    try:
        if db is None:
            return jsonify({'success': False, 'message': 'Database MySQL lokal belum terhubung'}), 500

        today_str = datetime.now().strftime('%Y-%m-%d')
        schedule = convert_timedelta_to_str(db.get_today_schedule())
        all_schedules = convert_timedelta_to_str(db.get_all_schedules())
        grace_period = db.get_grace_period_minutes()

        return jsonify({
            'success': True,
            'today': today_str,
            'today_schedule': schedule,
            'all_schedules': all_schedules,
            'grace_period_minutes': grace_period
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


# ========== ENDPOINT SINKRONISASI MASTER DATA DARI LARAVEL KE LOKAL ==========

def _get_laravel_url():
    if request.is_json and request.json and request.json.get('laravel_url'):
        return request.json.get('laravel_url')
    return request.args.get('laravel_url', SCANNER_SETTINGS.get('default_laravel_url', 'http://127.0.0.1:8000'))


@app.route('/api/python/sync/test-connection', methods=['GET', 'POST'])
def test_laravel_connection():
    try:
        from app.laravel_sync import LaravelSyncService
        laravel_url = _get_laravel_url()
        sync_service = LaravelSyncService(laravel_url, verify_ssl=False)
        result = sync_service.test_connection()
        return jsonify(result), (200 if result['success'] else 500)
    except Exception as e:
        return jsonify({'success': False, 'message': f'Error: {str(e)}'}), 500


@app.route('/api/python/sync/mahasiswa', methods=['GET', 'POST'])
def sync_mahasiswa_from_laravel():
    try:
        from app.laravel_sync import LaravelSyncService
        laravel_url = _get_laravel_url()
        sync_service = LaravelSyncService(laravel_url, verify_ssl=False)

        fetch_result = sync_service.fetch_mahasiswa()
        if not fetch_result['success']:
            return jsonify(fetch_result), 500

        sync_result = sync_service.sync_mahasiswa_to_local(fetch_result['data'])
        return jsonify({
            'success': True,
            'message': 'Sinkronisasi data peserta berhasil',
            'stats': sync_result
        })
    except Exception as e:
        return jsonify({'success': False, 'message': f'Error: {str(e)}'}), 500


@app.route('/api/python/sync/schedules', methods=['GET', 'POST'])
def sync_schedules_from_laravel():
    try:
        from app.laravel_sync import LaravelSyncService
        laravel_url = _get_laravel_url()
        sync_service = LaravelSyncService(laravel_url, verify_ssl=False)

        fetch_result = sync_service.fetch_schedules()
        if not fetch_result['success']:
            return jsonify(fetch_result), 500

        sync_result = sync_service.sync_schedules_to_local(fetch_result['data'])
        return jsonify({
            'success': True,
            'message': 'Sinkronisasi jadwal apel berhasil',
            'stats': sync_result
        })
    except Exception as e:
        return jsonify({'success': False, 'message': f'Error: {str(e)}'}), 500


@app.route('/api/python/sync/kegiatan', methods=['GET', 'POST'])
def sync_kegiatan_from_laravel():
    try:
        from app.laravel_sync import LaravelSyncService
        laravel_url = _get_laravel_url()
        sync_service = LaravelSyncService(laravel_url, verify_ssl=False)

        fetch_result = sync_service.fetch_kegiatan()
        if not fetch_result['success']:
            return jsonify(fetch_result), 500

        sync_result = sync_service.sync_kegiatan_to_local(fetch_result['data'])
        return jsonify({
            'success': True,
            'message': 'Sinkronisasi sesi kegiatan apel berhasil',
            'stats': sync_result
        })
    except Exception as e:
        return jsonify({'success': False, 'message': f'Error: {str(e)}'}), 500


@app.route('/api/python/sync/all', methods=['GET', 'POST'])
def sync_all_from_laravel():
    try:
        from app.laravel_sync import LaravelSyncService
        laravel_url = _get_laravel_url()
        sync_service = LaravelSyncService(laravel_url, verify_ssl=False)

        result = sync_service.sync_all()
        if result['success']:
            return jsonify({
                'success': True,
                'message': 'Sinkronisasi seluruh Master Data dari server berhasil!',
                'results': result
            })
        else:
            return jsonify({
                'success': False,
                'message': result.get('message', 'Sinkronisasi gagal'),
                'results': result
            }), 500
    except Exception as e:
        return jsonify({'success': False, 'message': f'Error: {str(e)}'}), 500


if __name__ == '__main__':
    print("=" * 64)
    print("  SIAPEL POLINELA — Sistem Presensi QR Scanner Lokal (Python)")
    print("  Mode: Alat Scanner QR (USB Keyboard / Serial COM) -> Sync Online")
    print("=" * 64)
    print("  Buka halaman Monitor Scanner Lokal di:")
    print("  -> http://127.0.0.1:5000/monitor")
    print("=" * 64)

    app.run(
        host='0.0.0.0',
        port=5000,
        debug=True,
        threaded=True
    )

