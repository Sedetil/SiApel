# SIAPEL POLINELA — Sistem Informasi Absensi Apel Berbasis RFID & QR Scanner Lokal

Sistem Informasi Absensi Apel Mahasiswa untuk **Politeknik Negeri Lampung (POLINELA)**.  
Aplikasi ini dirancang dengan **RFID sebagai alat presensi utama** *(tahap implementasi hardware menyusul)* serta dilengkapi **Sistem Presensi Lokal Python (`python_backend/`) berbasis Alat Scanner QR** yang sudah siap beroperasi secara lokal (*offline-first*) dan dapat di-*push* ke database server online.

> **Status Implementasi Perangkat Presensi:**
> 1. **Perangkat Utama — RFID Reader *(Belum Diimplementasikan / Tahap Integrasi Hardware)*:** Skema database (`rfid_cards`), pemetaan UID kartu di dashboard, serta endpoint API (`POST /api/v1/attendance/scan`) sudah disiapkan untuk integrasi perangkat RFID.
> 2. **Perangkat Aktif Saat Ini — Alat Scanner QR Lokal (`python_backend/`) *(Sudah Diimplementasikan)*:** Menggunakan Alat Scanner QR fisik (*USB HID Keyboard Emulation* & *Web Serial COM*) tanpa YOLO dan tanpa CCTV, berjalan di komputer lokal dengan MySQL lokal, backup Excel otomatis, dan fitur *push* ke database server online.

---

## 1. Fitur Utama

### A. Sistem Presensi Lokal Python (`python_backend/` — Aktif Saat Ini)
- **Pemindaian Alat Scanner QR Instan (Offline-First):**
  - Mendukung **Alat Scanner QR USB (HID Keyboard Emulation)** secara otomatis tanpa perlu mengklik kolom input, serta koneksi **Web Serial COM Port** dan input kode manual cadangan (juga kompatibel apabila kelak ditempelkan USB RFID Reader berjenis *keyboard emulation*).
  - Dilengkapi indikator suara *beep* otomatis berbasis Web Audio API (nada sukses, peringatan terlambat, dan error).
- **Sinkronisasi Master Data (`Sync Master Data`):**
  - Menarik daftar peserta/mahasiswa beserta identitas kartu/kode QR, jadwal apel, dan aturan batas keterlambatan dari Server Laravel (Online/Lokal) ke database **MySQL Lokal**.
- **Validasi Jadwal & Keterlambatan di Lokal:**
  - Memvalidasi waktu scan terhadap jadwal apel aktif (`attendance_start`, `late_threshold_time`, dan `attendance_end`) serta menghitung durasi keterlambatan dalam menit secara otomatis.
- **Backup Excel Lokal & Push ke Server Online (`Sync ke Server`):**
  - Otomatis membuat file *backup* Excel (`.xlsx`) di folder `python_backend/backups/` sebelum mengirim (*push*) batch data presensi ke database server online.

### B. Web Dashboard & Kesiapan Integrasi RFID Utama (Laravel)
- **Manajemen Kartu RFID & Peserta:**
  - Tabel `rfid_cards`, fitur pemetaan/penonaktifan UID kartu RFID per peserta, serta endpoint `POST /api/v1/attendance/scan` telah siap digunakan saat perangkat RFID diimplementasikan.
- **Otorisasi Multi-Peran (RBAC):**
  - **Admin:** Mengelola data peserta, pemetaan kartu RFID / kode QR, jadwal apel, akun petugas (Timdis), integrasi E-Kuliah, verifikasi izin/sakit, audit log, dan ekspor laporan (CSV & PDF).
  - **Petugas Apel (Timdis):** Memantau jalannya sesi apel secara *real-time*, memverifikasi pengajuan izin/sakit, dan menjalankan operasional presensi.
- **Audit Trail & Keamanan:**
  - Mencatat setiap *event* pemindaian (baik dari RFID maupun sinkronisasi QR lokal) di tabel `attendance_events` dan aktivitas manajemen di tabel `audit_logs`.

---

## 2. Tech Stack & Arsitektur

| Layer | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Utama (Online)** | PHP 8.3+, Laravel 13.x | MVC, Eloquent ORM, Laravel Sanctum |
| **Sistem Presensi Lokal** | Python 3.11+, Flask 3.x | Server lokal port `5000`, validasi waktu, backup Excel (`pandas` + `openpyxl`) |
| **Database** | MySQL 8.0+ | Database Server Online & Database MySQL Lokal (`siapel_db`) |
| **Web Frontend** | Blade, Vite 7.x, Tailwind CSS v4 | Mengacu pada spesifikasi token visual [StyleGuide.md](file:///c:/Users/bungt/Documents/Dody%20Setiawan/Kuliah/SiApel/StyleGuide.md) |
| **Testing & Style** | PHPUnit 12.x, Laravel Pint | Pengujian fitur otomatis & standar kode PSR-12 |

---

## 3. Susunan Struktur Direktori Proyek

Berikut adalah susunan lengkap file dan direktori pada repositori **SiApel** untuk panduan tim pengembang:

```plaintext
SiApel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AttendanceApiController.php   # Endpoint API v1 operasional apel
│   │   │   │   ├── AuthController.php            # Autentikasi token Sanctum API v1
│   │   │   │   └── SyncController.php            # Endpoint Sync Master Data & Push Presensi dari Python
│   │   │   ├── ApelScheduleController.php        # CRUD jadwal apel & aturan waktu presensi
│   │   │   ├── AttendanceController.php          # Live monitoring & riwayat presensi web
│   │   │   ├── AuditLogController.php            # Pemantauan jejak aktivitas (Audit Trail)
│   │   │   ├── AuthController.php                # Autentikasi sesi web (Login/Logout)
│   │   │   ├── DashboardController.php           # Statistik utama dashboard
│   │   │   ├── EKuliahController.php             # Integrasi sinkronisasi sistem E-Kuliah
│   │   │   ├── OfficerController.php             # Manajemen akun petugas (Timdis)
│   │   │   ├── ParticipantController.php         # Manajemen data mahasiswa/peserta & kartu
│   │   │   ├── PermissionRequestController.php   # Verifikasi pengajuan izin & sakit
│   │   │   ├── ProfileController.php             # Pengaturan profil & kata sandi pengguna
│   │   │   └── ReportController.php              # Rekapitulasi laporan & ekspor CSV/PDF
│   │   └── Middleware/
│   │       └── EnsureUserHasRole.php             # Proteksi hak akses berbasis role (admin/petugas)
│   ├── Models/
│   │   ├── ApelSchedule.php                      # Model jadwal apel & kalkulasi batas keterlambatan
│   │   ├── AttendanceEvent.php                   # Model log setiap kejadian pemindaian
│   │   ├── AttendanceRecord.php                  # Model catatan kehadiran mahasiswa per sesi
│   │   ├── AuditLog.php                          # Model jejak audit administratif
│   │   ├── EkuliahSyncLog.php                    # Model riwayat sinkronisasi E-Kuliah
│   │   ├── Participant.php                       # Model data mahasiswa/peserta apel
│   │   ├── PermissionRequest.php                 # Model permohonan izin/sakit
│   │   ├── RfidCard.php                          # Model identitas kartu/kode presensi peserta
│   │   ├── SystemSetting.php                     # Model konfigurasi sistem
│   │   └── User.php                              # Model akun pengguna (Admin & Petugas)
│   └── Services/
│       └── AttendanceService.php                 # Service inti pemrosesan dan validasi presensi
│
├── python_backend/                               # SISTEM PRESENSI ALAT SCANNER QR LOKAL (PYTHON)
│   ├── api_server.py                             # Entrypoint Flask server lokal (http://127.0.0.1:5000)
│   ├── requirements.txt                          # Daftar paket Python (Flask, MySQL, Pandas, OpenPyXL)
│   ├── README.md                                 # Panduan khusus modul Python lokal
│   ├── app/
│   │   ├── __init__.py
│   │   ├── config_db.py                          # Konfigurasi koneksi MySQL lokal & cooldown scanner
│   │   ├── database_manager.py                   # Inisialisasi tabel lokal & pencarian peserta via QR
│   │   ├── laravel_sync.py                       # Service penarik Master Data dari Server Laravel
│   │   ├── time_validator.py                     # Validasi jam buka, batas terlambat, & jam tutup apel
│   │   └── timezone_utils.py                     # Utilitas zona waktu Asia/Jakarta
│   ├── backups/                                  # Folder penyimpanan otomatis backup Excel (.xlsx) lokal
│   ├── static/
│   │   ├── css/
│   │   │   └── monitor.css                       # Styling halaman monitor QR Scanner sesuai StyleGuide
│   │   └── js/
│   │       ├── monitor.js                        # Logika Alat Scanner QR (USB/Serial), antrean, & Push Server
│   │       └── sync_master.js                    # Dialog & handler Sync Master Data dari Laravel
│   └── templates/
│       └── monitor.html                          # Antarmuka utama Monitor Presensi QR Scanner Lokal
│
├── database/
│   ├── factories/                                # Factory untuk pengujian otomatis
│   ├── migrations/                               # Skema tabel database MySQL
│   └── seeders/                                  # Seeder akun default, peserta, dan jadwal apel
│
├── resources/
│   ├── css/
│   │   └── app.css                               # Konfigurasi Tailwind CSS v4 & token desain
│   ├── js/
│   │   └── app.js                                # Script frontend utama Laravel
│   └── views/
│       ├── components/                           # Komponen Blade modular
│       ├── layouts/                              # Layout utama (app.blade.php, guest.blade.php)
│       └── pages/                                # Halaman web (dashboard, attendance, participants, dll.)
│
├── routes/
│   ├── api.php                                   # Route API Sinkronisasi Python (/api/sync/*) & API v1
│   ├── web.php                                   # Route halaman Web Dashboard Laravel
│   └── console.php                               # Perintah Artisan terjadwal
│
├── tests/
│   ├── Feature/
│   │   ├── AttendanceApiTest.php                 # Pengujian endpoint API presensi
│   │   ├── PythonSyncTest.php                    # Pengujian tarik Master Data & push presensi Python
│   │   └── WebDashboardTest.php                  # Pengujian halaman dashboard web
│   └── Unit/
│       └── AttendanceServiceTest.php             # Pengujian unit aturan bisnis presensi
│
├── AGENTS.md                                     # Instruksi standar pengembangan untuk AI & Developer
├── PRD.md                                        # Dokumen spesifikasi kebutuhan produk
├── StyleGuide.md                                 # Spesifikasi sistem desain & token warna POLINELA
└── README.md                                     # Dokumentasi utama proyek ini
```

---

## 4. Panduan Instalasi & Menjalankan Sistem

### A. Menjalankan Server Utama Laravel (Port 8000 / Online)

1. **Install dependensi PHP & Frontend:**
   ```bash
   composer install
   npm install
   ```

2. **Konfigurasi file `.env`:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan koneksi database di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=siapel_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Jalankan Migrasi & Seeder:**
   ```bash
   php artisan migrate --seed
   ```

4. **Jalankan Server Laravel & Vite:**
   ```bash
   npm run build
   php artisan serve
   ```
   Akses Dashboard Laravel di: `http://127.0.0.1:8000`

---

### B. Menjalankan Sistem Presensi QR Scanner Lokal Python (Port 5000)

1. **Masuk ke folder `python_backend` dan install paket Python:**
   ```bash
   cd python_backend
   pip install -r requirements.txt
   ```

2. **Jalankan Server Flask Python:**
   ```bash
   python api_server.py
   ```

3. **Buka Antarmuka Monitor Scanner QR Lokal:**
   - Buka browser ke: `http://127.0.0.1:5000/monitor`
   - Atau klik tombol **"Buka QR Scanner Lokal (Python)"** langsung dari menu **Live Monitoring** pada Dashboard Laravel.

---

## 5. Alur Kerja Sinkronisasi (Lokal ke Online)

```plaintext
┌──────────────────────────────────────┐         1. Sync Master Data (GET)        ┌──────────────────────────────────────┐
│    SISTEM PYTHON LOKAL (Port 5000)   │ ◄─────────────────────────────────────── │     SERVER LARAVEL ONLINE / UTAMA    │
│  - Alat Scanner QR (USB / Serial)    │    (/api/sync/mahasiswa & /schedules)    │  - Database Utama (Online)           │
│  - Database MySQL Lokal              │                                          │  - Dashboard Admin & Rekap Laporan   │
│  - Backup Excel Otomatis (.xlsx)     │ ───────────────────────────────────────► │  - Tabel attendance_records          │
└──────────────────────────────────────┘         2. Sync ke Server (POST)         └──────────────────────────────────────┘
                                                    (/api/sync)
```

1. **Persiapan Sebelum Apel (`Sync Master Data`):**
   - Pastikan laptop petugas terhubung ke internet/jaringan server.
   - Pada halaman `http://127.0.0.1:5000/monitor`, klik tombol **"Sync Master Data"**.
   - Masukkan URL Server Laravel (misal `http://127.0.0.1:8000` atau domain server online) lalu klik **Mulai Sinkronisasi**.
   - Data mahasiswa beserta jadwal apel aktif akan tersimpan di MySQL lokal.

2. **Pelaksanaan Presensi Apel di Lapangan (Lokal / Tanpa Internet):**
   - Hubungkan **Alat Scanner QR** ke port USB laptop.
   - Setiap kali kartu/QR mahasiswa dipindai oleh alat scanner, sistem Python lokal langsung memvalidasi jadwal & batas keterlambatan, membunyikan *beep*, menampilkan identitas mahasiswa di layar, dan menyimpannya ke antrean lokal.

3. **Push Data Presensi ke Server Online (`Sync ke Server`):**
   - Setelah sesi apel selesai (atau saat koneksi internet tersedia), klik tombol **"Sync ke Server"** di panel kanan.
   - Sistem otomatis menyimpan file **Backup Excel Lokal** di `python_backend/backups/absensi_apel_backup_YYYYMMDD_HHMMSS.xlsx`.
   - Seluruh antrean presensi dikirim ke endpoint `POST /api/sync` di server Laravel online dan langsung tercatat pada tabel `attendance_records`.

---

## 6. Endpoint API Sinkronisasi (Laravel <-> Python)

| Method | Endpoint Laravel | Fungsi |
| :--- | :--- | :--- |
| `GET` | `/api/sync/status` | Memeriksa koneksi dan jumlah statistik master data di server |
| `GET` | `/api/sync/mahasiswa` | Mengambil seluruh daftar peserta aktif beserta kode QR/kartu |
| `GET` | `/api/sync/schedules` | Mengambil daftar jadwal apel beserta jam buka & batas keterlambatan |
| `GET` | `/api/sync/kegiatan` | Mengambil daftar sesi kegiatan apel untuk pilihan dropdown lokal |
| `GET` | `/api/sync/system-config` | Mengambil konfigurasi sistem (termasuk *grace period*) |
| `POST` | `/api/sync` | Menerima *push* data presensi dari sistem Python lokal ke database online |

---

## 7. Akun Pengguna Bawaan (Default Credentials)

| Peran | Username / Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` (`admin@polinela.ac.id`) | `password123` | Akses penuh seluruh modul administrasi, peserta, jadwal, dan laporan |
| **Petugas** | `petugas1` (`petugas1@polinela.ac.id`) | `password123` | Akses operasional monitoring presensi & verifikasi izin/sakit |
| **Petugas** | `petugas2` (`petugas2@polinela.ac.id`) | `password123` | Akses operasional lapangan kedua |

---

## 8. Pengujian & Kualitas Kode

```bash
# Menjalankan seluruh automated test (termasuk PythonSyncTest):
php artisan test

# Memeriksa format standar koding PSR-12 dengan Laravel Pint:
vendor/bin/pint
```

---

## 9. Lisensi

Hak Cipta © 2026 **Politeknik Negeri Lampung (POLINELA)**. Seluruh hak cipta dilindungi undang-undang.
