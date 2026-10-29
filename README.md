# SIAPEL POLINELA (Sistem Informasi Absensi Apel Berbasis RFID)

Sistem Informasi Absensi Apel Kedisiplinan Terintegrasi Berbasis RFID untuk **Politeknik Negeri Lampung (POLINELA)**.  
Aplikasi ini dirancang untuk mencatat dan memvalidasi kehadiran apel mahasiswa secara real-time, cepat, dan presisi menggunakan kartu RFID/NFC, meminimalisir antrean, mencegah titip kartu/absensi ganda, menyediakan verifikasi izin/sakit digital, serta terintegrasi langsung dengan sistem akademik E-Kuliah POLINELA.

---

## 1. Fitur Utama Sistem

- **Struktur Rute Terisolasi Berbasis Role (RBAC):**
  - **Admin (`/admin/...`):**
    - Kelola Data Mahasiswa (NPM, Nama, Program Studi, Kompi/Pleton, Status Aktif).
    - Pemetaan kartu RFID dan aktivasi/deaktivasi kartu fisik mahasiswa.
    - Kelola Akun Tim Kedisiplinan (Timdis).
    - Manajemen Jadwal Apel: Generate jadwal rutin, jadwal khusus, penyesuaian toleransi keterlambatan, dan pembatalan apel.
    - Integrasi Sistem E-Kuliah POLINELA: Sinkronisasi master data mahasiswa dan pengiriman rekapitulasi kehadiran ke portal akademik.
    - Audit Trail & Log Aktivitas Sistem secara komprehensif.
  - **Tim Kedisiplinan / Timdis (`/timdis/...`):**
    - Dashboard Pemantauan Apel: Statistik kehadiran, rasio ketepatan waktu, dan grafik tren kehadiran adaptif.
    - Live Monitoring Scan Presensi: Pemantauan tap kartu real-time dengan Web Audio API feedback (suara konfirmasi).
    - Verifikasi & Validasi Permohonan Izin / Sakit: Peninjauan bukti surat dokter/alasan izin dan aksi persetujuan (*approval*) atau penolakan dengan catatan.
    - Riwayat Presensi & Ekspor Laporan: Unduh data kehadiran format Microsoft Excel / CSV berstandar UTF-8 BOM dan cetak dokumen PDF resmi.
- **Validasi Absensi Sisi Server (Server-Side Enforcement):**
  - Menggunakan waktu server tepercaya (`now()`), mencegah kecurangan manipulasi jam perangkat klien.
  - Penentuan status otomatis: **Hadir Tepat Waktu** (sebelum/tepat batas toleransi) atau **Terlambat** (melewati menit toleransi keterlambatan dengan perhitungan menit keterlambatan otomatis).
  - Validasi jendela waktu sesi absensi (`attendance_start` sampai `attendance_end`).
  - Pencegahan pemindaian duplikat secara idempoten melalui *database transaction lock* (`lockForUpdate`).
  - Mendukung alur **Check-In** (Absensi Masuk) dan **Check-Out** (Absensi Keluar).
- **Integrasi Hardware RFID & Multi-Klien:**
  - **Web Live Scanner:** Mendukung USB RFID Reader (USB HID Keyboard Emulation mode) tanpa driver tambahan.
  - **Klien Mobile / IoT:** REST API siap dihubungkan dengan aplikasi Android (NFC internal / USB OTG) maupun microcontroller mandiri (ESP32 / RC522).
- **Kepatuhan Class Diagram & Standar NPM POLINELA:**
  - Entitas database dan model telah sepenuhnya diselaraskan dengan spesifikasi Class Diagram resmi (`Pengguna`, `Mahasiswa`, `Apel`, `Presensi`, `PengajuanIzin`, `LogSinkronisasi`, dan antarmuka eksternal `SistemEKuliah`).
  - Menggunakan identitas **NPM** (Nomor Pokok Mahasiswa) di seluruh basis data, antarmuka, validasi, dan laporan rekapitulasi.

---

## 2. Tech Stack & Arsitektur

| Layer | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend** | PHP 8.4+, Laravel 13.x | MVC Architecture, Eloquent ORM, Laravel Sanctum v4 |
| **Database** | MySQL 8.0+ | Database `siapel_db` dengan tabel fisik Class Diagram |
| **Frontend** | Blade Templates, Vite 7.x, Tailwind CSS v4 | Berdasarkan spesifikasi desain [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/StyleGuide.md) |
| **Icons & UI** | Lucide Icons, Google Fonts (DM Sans & IBM Plex Mono) | Tipografi formal & angka statistik kehadiran kontras tinggi |
| **Testing** | PHPUnit 12.x | **54 automated unit & feature tests (100% Passed)** |
| **Code Style** | Laravel Pint | Standar kode PSR-12 / Laravel Preset |

---

## 3. Akun Pengguna Bawaan (Default Credentials)

Setelah menjalankan seeder database, sistem menyediakan akun default berikut untuk pengujian lokal:

| Peran | Username | Password Default | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password123` | Akses rute `/admin/*` (Kelola Mahasiswa, Timdis, Jadwal Apel, E-Kuliah, Audit Trail) |
| **Timdis** | `timdis` / `petugas1` | `password123` | Akses rute `/timdis/*` (Monitoring Scan, Verifikasi Izin/Sakit, Laporan & Ekspor) |
| **Timdis 2** | `petugas2` | `password123` | Petugas operasional pemantauan lapangan kedua |

> **Tips:** Pengguna juga dapat masuk menggunakan alamat email resmi (contoh: `admin@polinela.ac.id` atau `timdis@polinela.ac.id`).

---

## 4. Panduan Instalasi & Menjalankan Sistem

### Persyaratan Lingkungan
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`)
- MySQL Server >= 8.0
- Composer >= 2.x
- Node.js >= 18.x & NPM

### Langkah Setup
1. **Clone repositori dan install dependensi backend:**
   ```bash
   composer install
   ```

2. **Install dependensi frontend:**
   ```bash
   npm install
   ```

3. **Konfigurasi Environment File (`.env`):**
   Salin file `.env.example` ke `.env` lalu sesuaikan koneksi database Anda:
   ```env
   APP_NAME="SIAPEL POLINELA"
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=siapel_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi Database & Seeder:**
   ```bash
   php artisan migrate --seed
   ```

6. **Kompilasi Aset Frontend (Tailwind CSS v4 & JS):**
   ```bash
   npm run build
   # Atau untuk mode pengembangan dengan live-reload:
   npm run dev
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka peramban di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 5. Menjalankan Pengujian Otomatis (Automated Testing)

Sistem telah dilengkapi dengan test suite lengkap (54 test cases, 252 assertions) tanpa kegagalan:

```bash
# Menjalankan seluruh test suite PHPUnit:
php vendor/phpunit/phpunit/phpunit
# Atau melalui artisan:
php artisan test
```

### Ruang Lingkup Pengujian:
- **Class Diagram Alignment:** Validasi seluruh operasi dan relasi class `Pengguna`, `Mahasiswa`, `Apel`, `Presensi`, `PengajuanIzin`, `LogSinkronisasi`, serta integrasi `SistemEKuliah`.
- **Role Isolation & Navigation:** Penegasan proteksi middleware antar rute `/admin` dan `/timdis` serta larangan akses lintas role.
- **Attendance Core Logic:** Perhitungan menit keterlambatan, penanganan jendela waktu absensi, idempotensi duplikasi scan, dan status alpa.
- **REST API:** Login Sanctum token, pembacaan jadwal aktif, dan pemrosesan pemindaian RFID.
- **Fitur Persetujuan Izin/Sakit:** Verifikasi alur persetujuan (*approval*) dan penolakan izin dengan pencatatan audit log.

Untuk merapikan format kode sesuai standar PSR-12:
```bash
vendor/bin/pint
```

---

## 6. Dokumentasi REST API (v1)

Seluruh endpoint API beralamat di `/api/v1/` dan mengembalikan respons terstandarisasi dalam format JSON.

### A. Autentikasi API

#### `POST /api/v1/auth/login`
- **Tujuan:** Autentikasi petugas / admin untuk mendapatkan Bearer token Sanctum.
- **Request Body:**
  ```json
  {
    "identifier": "petugas1",
    "password": "password123",
    "device_name": "Android-Pixel-Scanner"
  }
  ```
- **Response Sukses (200 OK):**
  ```json
  {
    "success": true,
    "message": "Login berhasil.",
    "token": "1|qX8y...example_token",
    "user": {
      "id": 2,
      "name": "Petugas Kedisiplinan 1",
      "username": "petugas1",
      "email": "petugas1@polinela.ac.id",
      "role": "petugas"
    }
  }
  ```

#### `GET /api/v1/auth/me` *(Auth: Bearer Token)*
- Mengembalikan profil akun pengguna yang sedang terautentikasi.

#### `POST /api/v1/auth/logout` *(Auth: Bearer Token)*
- Mencabut dan menghapus Bearer token aktif.

---

### B. Jadwal Apel & Pemindaian Absensi

#### `GET /api/v1/schedules/active` *(Auth: Bearer Token)*
- **Tujuan:** Mengambil daftar sesi apel yang berstatus aktif atau dijadwalkan pada hari ini.

#### `GET /api/v1/schedules/{id}/summary` *(Auth: Bearer Token)*
- **Tujuan:** Mengambil ringkasan data statistik kehadiran per sesi apel secara langsung.

#### `POST /api/v1/attendance/scan` *(Auth: Bearer Token)*
- **Tujuan:** Memvalidasi kartu RFID dan mencatat presensi mahasiswa.
- **Request Body:**
  ```json
  {
    "apel_schedule_id": 1,
    "card_uid": "E2000019",
    "scan_type": "check_in",
    "device_identifier": "Android-Pixel-Scanner"
  }
  ```
- **Response Sukses (200 OK - Hadir Tepat Waktu):**
  ```json
  {
    "success": true,
    "code": "CHECK_IN_SUCCESS",
    "message": "Absensi berhasil: Hadir Tepat Waktu (Ahmad Fauzi)",
    "attendance": {
      "status": "hadir",
      "menit_terlambat": 0,
      "metode": "RFID",
      "check_in_time": "06:55:12",
      "late_threshold": "07:15:00"
    },
    "participant": {
      "identifier": "22753001",
      "name": "Ahmad Fauzi",
      "study_program": "Manajemen Informatika",
      "class_group": "Kompi A - Pleton 1"
    }
  }
  ```

#### `GET /api/v1/participants/lookup?identifier=22753001` *(Auth: Bearer Token)*
- Mencari data mahasiswa berdasarkan NPM atau Card UID.

---

## 7. Petunjuk Penggunaan Hardware RFID

### A. Penggunaan Web Live Scanner (Laptop / PC Timdis)
1. Masuk ke Web Dashboard sebagai Admin atau Timdis.
2. Akses menu **Live Monitoring Apel** (`/admin/monitoring` atau `/timdis/monitoring`).
3. Pilih jadwal apel aktif hari ini.
4. Pasang **USB RFID Reader (13.56 MHz / 125 kHz)** ke port USB laptop.
   - Reader bekerja dengan mode *USB HID Keyboard Emulation*.
   - Saat kartu didekatkan, reader secara otomatis membaca UID kartu dan mengirimkan sinyal *Enter* (`\n`).
5. Arahkan kursor ke kotak tap kartu (sistem juga memfokuskan kursor secara otomatis).
6. Saat kartu mahasiswa ditempelkan:
   - Data absensi langsung diverifikasi ke server.
   - Banner status kehadiran muncul instan (Hijau = Hadir, Kuning = Terlambat, Merah = Ditolak / Duplikat).
   - Audio feedback berbunyi otomatis sehingga petugas tidak harus menatap layar terus-menerus.

---

## 8. Struktur Direktori Proyek

```plaintext
SiApel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/ (AuthController.php, AttendanceApiController.php)
│   │   │   ├── ApelScheduleController.php      # Manajemen jadwal apel & pembatalan
│   │   │   ├── AttendanceController.php        # Monitoring scan presensi & riwayat
│   │   │   ├── AuditLogController.php          # Audit trail aktivitas pengguna
│   │   │   ├── AuthController.php              # Autentikasi web & login redirect
│   │   │   ├── DashboardController.php         # Dashboard analitik & tren kehadiran
│   │   │   ├── EKuliahController.php           # Konfigurasi & sinkronisasi E-Kuliah
│   │   │   ├── OfficerController.php           # CRUD pengelolaan akun Timdis
│   │   │   ├── ParticipantController.php       # CRUD data mahasiswa & pemetaan RFID
│   │   │   ├── PermissionRequestController.php # Persetujuan izin & sakit
│   │   │   ├── ProfileController.php           # Profil akun & ganti kata sandi
│   │   │   └── ReportController.php            # Rekapitulasi & ekspor CSV/Excel/PDF
│   │   └── Middleware/
│   │       └── EnsureUserHasRole.php           # Proteksi isolasi role (admin vs timdis)
│   ├── Models/
│   │   ├── Concerns/MapsLegacyColumns.php      # Dynamic column translation trait
│   │   ├── Apel.php / ApelSchedule.php         # Model jadwal apel
│   │   ├── AttendanceEvent.php                 # Log audit scan RFID
│   │   ├── AttendanceRecord.php / Presensi.php # Model data kehadiran presensi
│   │   ├── AuditLog.php                        # Catatan audit aktivitas sistem
│   │   ├── EkuliahSyncLog.php                  # Riwayat sinkronisasi data akademik
│   │   ├── Mahasiswa.php / Participant.php     # Model data mahasiswa (NPM)
│   │   ├── MappedEloquentBuilder.php           # Custom Eloquent query builder
│   │   ├── PengajuanIzin.php                   # Model permohonan izin/sakit
│   │   ├── Pengguna.php / User.php             # Model akun pengguna sistem
│   │   ├── RfidCard.php                        # Model relasi kartu fisik RFID
│   │   └── SystemSetting.php                   # Pengaturan konfigurasi sistem
│   └── Services/
│       ├── AttendanceService.php               # Logika inti pemindaian & validasi RFID
│       └── EKuliahService.php                  # Service integrasi eksternal Sistem E-Kuliah
├── database/
│   ├── migrations/                             # Skema tabel database (pengguna, mahasiswa, apel, presensi, pengajuan_izin, log_sinkronisasi)
│   └── seeders/                                # Seeder akun bawaan & data mock
├── resources/
│   ├── css/app.css                             # Tailwind CSS v4 design tokens
│   ├── js/app.js                               # Web Audio API & RFID scanner script
│   └── views/                                  # Blade templates responsif
│       ├── layouts/                            # Layout utama & sidebar navigasi role
│       └── pages/                              # Halaman dashboard, mahasiswa, apel, izin, laporan
├── routes/
│   ├── api.php                                 # REST API v1 endpoints
│   └── web.php                                 # Rute terisolasi /admin dan /timdis
├── tests/                                      # 54 Automated unit & feature test cases
├── AGENTS.md                                   # Panduan operasional AI coding assistant
├── PRD.md                                      # Dokumen Kebutuhan Produk
├── StyleGuide.md                               # Panduan Token Desain Visual POLINELA
└── README.md                                   # Dokumentasi resmi ini
```

---

## 9. Lisensi & Hak Cipta

Hak Cipta © 2026 **Politeknik Negeri Lampung (POLINELA)**. Seluruh hak cipta dilindungi undang-undang.
