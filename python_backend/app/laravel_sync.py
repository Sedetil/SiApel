"""
Laravel Sync Service untuk SIAPEL POLINELA
Menarik data master (Peserta, Jadwal Apel, Sesi Kegiatan, Konfigurasi) dari Server Laravel
dan menyimpannya ke database MySQL lokal Python untuk pemindaian QR Scanner berkecepatan tinggi.
"""

import requests
import logging
from typing import Dict, List
import mysql.connector

from app.config_db import MYSQL_CONFIG

logger = logging.getLogger('LaravelSync')


class LaravelSyncService:
    def __init__(self, laravel_base_url: str = "http://127.0.0.1:8000", verify_ssl: bool = True):
        self.base_url = laravel_base_url.rstrip('/')
        self.verify_ssl = verify_ssl
        self.session = requests.Session()
        self.session.headers.update({
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'User-Agent': 'SiApel-Python-QR-Sync/1.0'
        })

        if not verify_ssl:
            import urllib3
            urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

    def _get_conn(self):
        return mysql.connector.connect(**MYSQL_CONFIG)

    def _execute(self, query, params=None, fetch_one=False, fetch_all=False):
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

    def test_connection(self) -> Dict:
        """Test koneksi ke Server Laravel SiApel"""
        try:
            response = self.session.get(
                f"{self.base_url}/api/sync/status",
                timeout=10,
                verify=self.verify_ssl
            )
            response.raise_for_status()
            return {
                'success': True,
                'message': 'Koneksi ke Server SiApel berhasil',
                'data': response.json()
            }
        except requests.exceptions.SSLError:
            return {
                'success': False,
                'message': 'SSL Error: Sertifikat tidak valid. Coba gunakan HTTP atau periksa konfigurasi HTTPS server.'
            }
        except requests.exceptions.Timeout:
            return {
                'success': False,
                'message': f'Timeout: Server tidak merespons dalam 10 detik ({self.base_url}).'
            }
        except requests.exceptions.ConnectionError:
            return {
                'success': False,
                'message': f'Gagal terhubung ke Server Laravel di {self.base_url}. Pastikan server aktif.'
            }
        except requests.exceptions.HTTPError as e:
            return {
                'success': False,
                'message': f'HTTP Error {e.response.status_code}: Pastikan endpoint /api/sync/status tersedia di {self.base_url}.'
            }
        except requests.exceptions.RequestException as e:
            return {
                'success': False,
                'message': f'Gagal terhubung ke Laravel: {str(e)}'
            }

    def fetch_mahasiswa(self) -> Dict:
        """Menarik data peserta/mahasiswa dari Laravel API"""
        try:
            response = self.session.get(
                f"{self.base_url}/api/sync/mahasiswa",
                timeout=60,
                verify=self.verify_ssl
            )
            response.raise_for_status()
            data = response.json()
            if not data.get('success'):
                return {'success': False, 'message': data.get('message', 'Gagal mengambil data peserta')}

            mahasiswa_list = data.get('data', [])
            return {
                'success': True,
                'data': mahasiswa_list,
                'count': len(mahasiswa_list)
            }
        except requests.exceptions.RequestException as e:
            logger.error(f"Error fetching mahasiswa: {e}")
            return {'success': False, 'message': f'Error mengambil data peserta: {str(e)}'}

    def fetch_schedules(self) -> Dict:
        """Menarik jadwal apel dari Laravel API"""
        try:
            response = self.session.get(
                f"{self.base_url}/api/sync/schedules",
                timeout=30,
                verify=self.verify_ssl
            )
            response.raise_for_status()
            data = response.json()
            if not data.get('success'):
                return {'success': False, 'message': data.get('message', 'Gagal mengambil jadwal apel')}

            schedules = data.get('data', [])
            return {
                'success': True,
                'data': schedules,
                'count': len(schedules)
            }
        except requests.exceptions.RequestException as e:
            logger.error(f"Error fetching schedules: {e}")
            return {'success': False, 'message': f'Error mengambil jadwal apel: {str(e)}'}

    def fetch_kegiatan(self) -> Dict:
        """Menarik daftar kegiatan/sesi apel dari Laravel API"""
        try:
            response = self.session.get(
                f"{self.base_url}/api/sync/kegiatan",
                timeout=30,
                verify=self.verify_ssl
            )
            response.raise_for_status()
            data = response.json()
            if not data.get('success'):
                return {'success': False, 'message': data.get('message', 'Gagal mengambil kegiatan apel')}

            kegiatan_list = data.get('data', [])
            return {
                'success': True,
                'data': kegiatan_list,
                'count': len(kegiatan_list)
            }
        except requests.exceptions.RequestException as e:
            logger.error(f"Error fetching kegiatan: {e}")
            return {'success': False, 'message': f'Error mengambil kegiatan apel: {str(e)}'}

    def fetch_system_config(self) -> Dict:
        """Menarik konfigurasi sistem dari Laravel API"""
        try:
            response = self.session.get(
                f"{self.base_url}/api/sync/system-config",
                timeout=30,
                verify=self.verify_ssl
            )
            response.raise_for_status()
            data = response.json()
            if not data.get('success'):
                return {'success': False, 'message': data.get('message', 'Gagal mengambil system config')}

            config_list = data.get('data', [])
            return {
                'success': True,
                'data': config_list,
                'count': len(config_list)
            }
        except requests.exceptions.RequestException as e:
            logger.error(f"Error fetching system config: {e}")
            return {'success': False, 'message': f'Error mengambil system config: {str(e)}'}

    def sync_mahasiswa_to_local(self, mahasiswa_list: List[Dict]) -> Dict:
        """Simpan/perbarui data peserta ke tabel `mahasiswa` di MySQL lokal"""
        inserted = 0
        updated = 0
        errors = 0

        for mhs in mahasiswa_list:
            try:
                mhs_id = str(mhs.get('id') or mhs.get('identifier'))
                qr_code_id = str(mhs.get('qr_code_id') or mhs_id)

                existing = self._execute(
                    "SELECT id FROM mahasiswa WHERE id = %s",
                    (mhs_id,),
                    fetch_one=True
                )

                if existing:
                    self._execute("""
                        UPDATE mahasiswa SET
                            name = %s,
                            kompi = %s,
                            jurusan = %s,
                            prodi = %s,
                            email = %s,
                            no_telp_mahasiswa = %s,
                            no_telp_ortu = %s,
                            qr_code_id = %s,
                            is_active = %s
                        WHERE id = %s
                    """, (
                        mhs.get('name', '-'),
                        mhs.get('kompi') or '-',
                        mhs.get('jurusan') or '-',
                        mhs.get('prodi') or '-',
                        mhs.get('email'),
                        mhs.get('no_telp_mahasiswa') or '',
                        mhs.get('no_telp_ortu') or '',
                        qr_code_id,
                        mhs.get('is_active', 1),
                        mhs_id
                    ))
                    updated += 1
                else:
                    self._execute("""
                        INSERT INTO mahasiswa (
                            id, name, kompi, jurusan, prodi, email,
                            no_telp_mahasiswa, no_telp_ortu, qr_code_id, is_active
                        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    """, (
                        mhs_id,
                        mhs.get('name', '-'),
                        mhs.get('kompi') or '-',
                        mhs.get('jurusan') or '-',
                        mhs.get('prodi') or '-',
                        mhs.get('email'),
                        mhs.get('no_telp_mahasiswa') or '',
                        mhs.get('no_telp_ortu') or '',
                        qr_code_id,
                        mhs.get('is_active', 1)
                    ))
                    inserted += 1
            except Exception as e:
                logger.error(f"Error syncing mahasiswa {mhs.get('id')}: {e}")
                errors += 1

        return {
            'success': True,
            'inserted': inserted,
            'updated': updated,
            'errors': errors,
            'total': len(mahasiswa_list)
        }

    def sync_schedules_to_local(self, schedules: List[Dict]) -> Dict:
        """Simpan/perbarui jadwal apel ke MySQL lokal"""
        inserted = 0
        updated = 0
        errors = 0

        for schedule in schedules:
            try:
                tanggal_str = str(schedule.get('tanggal', ''))[:10]
                existing = self._execute(
                    "SELECT id FROM pkkmb_schedules WHERE id = %s",
                    (schedule['id'],),
                    fetch_one=True
                )

                if existing:
                    self._execute("""
                        UPDATE pkkmb_schedules SET
                            hari_ke = %s,
                            title = %s,
                            tanggal = %s,
                            check_in_start = %s,
                            check_in_end = %s,
                            attendance_end = %s,
                            check_out_start = %s,
                            check_out_end = %s,
                            is_active = %s
                        WHERE id = %s
                    """, (
                        schedule.get('hari_ke', 1),
                        schedule.get('title', 'Apel Mahasiswa'),
                        tanggal_str,
                        schedule.get('check_in_start'),
                        schedule.get('check_in_end'),
                        schedule.get('attendance_end'),
                        schedule.get('check_out_start', '07:30:00'),
                        schedule.get('check_out_end', '23:59:59'),
                        schedule.get('is_active', 1),
                        schedule['id']
                    ))
                    updated += 1
                else:
                    self._execute("""
                        INSERT INTO pkkmb_schedules (
                            id, hari_ke, title, tanggal, check_in_start, check_in_end,
                            attendance_end, check_out_start, check_out_end, is_active
                        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    """, (
                        schedule['id'],
                        schedule.get('hari_ke', 1),
                        schedule.get('title', 'Apel Mahasiswa'),
                        tanggal_str,
                        schedule.get('check_in_start'),
                        schedule.get('check_in_end'),
                        schedule.get('attendance_end'),
                        schedule.get('check_out_start', '07:30:00'),
                        schedule.get('check_out_end', '23:59:59'),
                        schedule.get('is_active', 1)
                    ))
                    inserted += 1
            except Exception as e:
                logger.error(f"Error syncing schedule {schedule.get('id')}: {e}")
                errors += 1

        return {
            'success': True,
            'inserted': inserted,
            'updated': updated,
            'errors': errors,
            'total': len(schedules)
        }

    def sync_kegiatan_to_local(self, kegiatan_list: List[Dict]) -> Dict:
        """Simpan/perbarui daftar kegiatan/sesi apel ke MySQL lokal"""
        inserted = 0
        updated = 0
        errors = 0

        for kegiatan in kegiatan_list:
            try:
                tanggal_str = str(kegiatan.get('tanggal_pelaksanaan', ''))[:10]
                existing = self._execute(
                    "SELECT id FROM kegiatan WHERE id = %s",
                    (kegiatan['id'],),
                    fetch_one=True
                )

                if existing:
                    self._execute("""
                        UPDATE kegiatan SET
                            nama = %s,
                            tanggal_pelaksanaan = %s,
                            is_active = %s
                        WHERE id = %s
                    """, (
                        kegiatan.get('nama'),
                        tanggal_str,
                        kegiatan.get('is_active', 1),
                        kegiatan['id']
                    ))
                    updated += 1
                else:
                    self._execute("""
                        INSERT INTO kegiatan (id, nama, tanggal_pelaksanaan, is_active)
                        VALUES (%s, %s, %s, %s)
                    """, (
                        kegiatan['id'],
                        kegiatan.get('nama'),
                        tanggal_str,
                        kegiatan.get('is_active', 1)
                    ))
                    inserted += 1
            except Exception as e:
                logger.error(f"Error syncing kegiatan {kegiatan.get('id')}: {e}")
                errors += 1

        return {
            'success': True,
            'inserted': inserted,
            'updated': updated,
            'errors': errors,
            'total': len(kegiatan_list)
        }

    def sync_system_config_to_local(self, config_list: List[Dict]) -> Dict:
        """Simpan/perbarui konfigurasi sistem ke MySQL lokal"""
        inserted = 0
        updated = 0
        errors = 0

        for config in config_list:
            try:
                config_key = config.get('config_key')
                config_value = config.get('config_value')
                description = config.get('description', '')

                existing = self._execute(
                    "SELECT id FROM system_config WHERE config_key = %s",
                    (config_key,),
                    fetch_one=True
                )

                if existing:
                    self._execute("""
                        UPDATE system_config SET
                            config_value = %s,
                            description = %s
                        WHERE config_key = %s
                    """, (config_value, description, config_key))
                    updated += 1
                else:
                    self._execute("""
                        INSERT INTO system_config (config_key, config_value, description)
                        VALUES (%s, %s, %s)
                    """, (config_key, config_value, description))
                    inserted += 1
            except Exception as e:
                logger.error(f"Error syncing system config {config.get('config_key')}: {e}")
                errors += 1

        return {
            'success': True,
            'inserted': inserted,
            'updated': updated,
            'errors': errors,
            'total': len(config_list)
        }

    def sync_all(self) -> Dict:
        """Sinkronisasi seluruh Master Data dari Server Laravel ke MySQL Lokal"""
        results = {
            'success': True,
            'mahasiswa': {'success': False},
            'schedules': {'success': False},
            'kegiatan': {'success': False},
            'system_config': {'success': False}
        }

        conn_test = self.test_connection()
        if not conn_test['success']:
            results['success'] = False
            results['message'] = conn_test['message']
            return results

        mhs_fetch = self.fetch_mahasiswa()
        if mhs_fetch['success']:
            results['mahasiswa'] = self.sync_mahasiswa_to_local(mhs_fetch['data'])
        else:
            results['mahasiswa'] = mhs_fetch
            results['success'] = False

        sch_fetch = self.fetch_schedules()
        if sch_fetch['success']:
            results['schedules'] = self.sync_schedules_to_local(sch_fetch['data'])
        else:
            results['schedules'] = sch_fetch
            results['success'] = False

        keg_fetch = self.fetch_kegiatan()
        if keg_fetch['success']:
            results['kegiatan'] = self.sync_kegiatan_to_local(keg_fetch['data'])
        else:
            results['kegiatan'] = keg_fetch
            results['success'] = False

        cfg_fetch = self.fetch_system_config()
        if cfg_fetch['success']:
            results['system_config'] = self.sync_system_config_to_local(cfg_fetch['data'])
        else:
            results['system_config'] = cfg_fetch

        return results

