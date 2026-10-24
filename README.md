# SIAPEL POLINELA (Sistem Informasi Absensi Apel Berbasis RFID)

Sistem Informasi Absensi Apel Terintegrasi Berbasis RFID untuk Politeknik Negeri Lampung (POLINELA).  
Aplikasi ini dirancang untuk mencatat dan memvalidasi kehadiran apel secara real-time, cepat, dan akurat menggunakan pemindaian kartu RFID/NFC, meminimalisir antrean, mencegah kecurangan (*titip kartu* / absensi ganda), serta menyediakan rekapitulasi data kehadiran otomatis.

---

## 1. Fitur Utama

- **Otorisasi Multi-Peran (RBAC):**
  - **Admin:** Pengelolaan peserta (NPM, Nama, Prodi, Kompi), pemetaan/penggantian kartu RFID, manajemen jadwal apel, pengelolaan akun petugas, pemantauan audit log, dan ekspor laporan (CSV/Excel & PDF).
  - **Petugas Apel:** Menjalankan sesi absensi apel aktif melalui aplikasi Android maupun Web Scanner, memantau statistik kehadiran real-time, dan melakukan pencarian cepat peserta.
- **Validasi Absensi Sisi Server (Server-Side Enforcement):**
  - Jam validasi sepenuhnya menggunakan waktu server (`now()`), mencegah manipulasi waktu dari sisi klien/perangkat.
  - Penentuan status otomatis: **Hadir** (sebelum/tepat batas toleransi) atau **Terlambat** (melewati batas toleransi keterlambatan).
  - Pengecekan jendela waktu absensi (`attendance_start` sampai `attendance_end`).
  - Pencegahan pemindaian duplikat secara idempoten dengan *database lock* & *unique constraint*.
  - Dukungan mode **Check-In** (Absensi Masuk) dan **Check-Out** (Absensi Selesai Apel).
- **Integrasi Hardware RFID Fleksibel:**
  - **Web Scanner:** Mendukung USB RFID Reader (USB HID Keyboard Emulation mode) yang langsung membaca UID kartu tanpa instalasi driver khusus, dilengkapi audio feedback Web Audio API (beep sukses/gagal).
  - **Aplikasi Android:** Mendukung pemindaian via chip NFC bawaan smartphone (ISO 14443-A / Mifare) serta USB RFID Reader OTG.
- **Audit Trail & Keamanan:**
  - Pencatatan seluruh *event* tap kartu pada tabel `attendance_events` (termasuk tap ditolak, kartu tidak dikenal, atau di luar jadwal).
  - Pencatatan aktivitas administratif pengguna pada tabel `audit_logs`.
  - Autentikasi API berbasis token aman Laravel Sanctum.
  - Sanitasi input dan proteksi CSRF, XSS, SQL Injection, dan IDOR.

---

## 2. Tech Stack & Arsitektur

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend** | PHP 8.4+, Laravel 13.x | MVC, Eloquent ORM, Laravel Sanctum v4 |
| **Database** | MySQL 8.0+ | Database `siapel_db` |
| **Web Frontend** | Blade Templates, Vite 7.x, Tailwind CSS v4 | Antarmuka responsif mengacu pada [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/StyleGuide.md) |
| **Testing** | PHPUnit 12.x | 28 automated feature & unit tests |
| **Code Style** | Laravel Pint | Standar PSR-12 |

---

## 3. Akun Pengguna Bawaan (Default Credentials)

Setelah menjalankan seeder database, sistem menyediakan akun default berikut:

| Peran | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password123` | Akses penuh dashboard administrasi, CRUD peserta, jadwal, dan laporan |
| **Petugas** | `petugas1` | `password123` | Akses operasional pemantauan absensi web & login aplikasi Android |
| **Petugas** | `petugas2` | `password123` | Akses operasional lapangan kedua |

> Catatan: Pengguna juga dapat login menggunakan alamat email (misal: `admin@polinela.ac.id`).

---

## 4. Panduan Instalasi & Menjalankan Web Dashboard

### Persyaratan Sistem
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`)
- MySQL Server >= 8.0
- Composer >= 2.x
- Node.js >= 18.x & NPM

### Langkah-langkah Setup
1. **Clone repositori dan install dependensi backend:**
   ```bash
   composer install
   ```

2. **Install dependensi frontend:**
   ```bash
   npm install
   ```

3. **Konfigurasi Lingkungan (`.env`):**
   Salin file `.env.example` ke `.env` lalu sesuaikan konfigurasi database Anda:
   ```env
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

7. **Jalankan Local Development Server:**
   ```bash
   php artisan serve
   ```
   Buka browser di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 5. Menjalankan Pengujian Otomatis (Automated Testing)

Sistem telah dilengkapi dengan test suite lengkap yang mencakup unit test logika absensi, pengujian endpoint REST API, serta feature test web dashboard:

```bash
# Menjalankan seluruh test suite PHPUnit:
php vendor/phpunit/phpunit/phpunit
# Atau via artisan:
php artisan test
```

Semua 28 test mencakup pengujian:
- Autentikasi & kontrol peran (Admin vs Petugas)
- Pencegahan akses akun nonaktif
- Validasi waktu keterlambatan (*on time* vs *late*)
- Idempotensi & pencegahan scan duplikat
- Penanganan kartu tidak terdaftar & peserta nonaktif
- Validasi jendela waktu sesi absensi
- Alur check-in dan check-out
- Ekspor CSV dan cetak PDF

Untuk memeriksa dan memperbaiki format kode:
```bash
vendor/bin/pint
```

---

## 6. Dokumentasi REST API

Semua endpoint API beralamat di `/api/v1/` dan mengembalikan respons berformat JSON.

### A. Autentikasi

#### `POST /api/v1/auth/login`
- **Tujuan:** Autentikasi petugas apel / admin dan mendapatkan Bearer token Sanctum.
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
    "message": "Autentikasi berhasil.",
    "token": "1|qX8y...example_token",
    "user": {
      "id": 2,
      "name": "Petugas Apel 1",
      "username": "petugas1",
      "email": "petugas1@polinela.ac.id",
      "role": "petugas"
    }
  }
  ```

#### `GET /api/v1/auth/me` *(Auth: Bearer Token)*
- Mengembalikan informasi profil pengguna yang sedang login.

#### `POST /api/v1/auth/logout` *(Auth: Bearer Token)*
- Mencabut token otorisasi aktif.

---

### B. Jadwal Apel & Pemindaian Absensi

#### `GET /api/v1/schedules/active` *(Auth: Bearer Token)*
- **Tujuan:** Mengambil daftar sesi apel yang sedang aktif atau dijadwalkan hari ini.
- **Response Sukses (200 OK):**
  ```json
  {
    "success": true,
    "schedules": [
      {
        "id": 1,
        "title": "Apel Pagi Kedisiplinan Mahasiswa",
        "schedule_date": "2026-10-22",
        "start_time": "07:00:00",
        "end_time": "08:00:00",
        "attendance_start": "06:30:00",
        "attendance_end": "07:45:00",
        "late_threshold_time": "07:15:00",
        "location": "Lapangan Utama Kampus POLINELA",
        "status": "active"
      }
    ]
  }
  ```

#### `GET /api/v1/schedules/{id}/summary` *(Auth: Bearer Token)*
- **Tujuan:** Mengambil rekap statistik kehadiran per sesi apel secara real-time.
- **Response Sukses (200 OK):**
  ```json
  {
    "success": true,
    "schedule": { "id": 1, "title": "Apel Pagi Kedisiplinan Mahasiswa" },
    "statistics": {
      "total_participants": 9,
      "total_recorded": 5,
      "total_hadir": 4,
      "total_terlambat": 1,
      "attendance_percentage": 55.6
    }
  }
  ```

#### `POST /api/v1/attendance/scan` *(Auth: Bearer Token)*
- **Tujuan:** Memvalidasi dan mencatat tap kartu RFID secara real-time.
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
    "message": "Absensi Ahmad Rizki Pratama berhasil dicatat (hadir).",
    "attendance": {
      "id": 12,
      "status": "hadir",
      "check_in_time": "2026-10-22 07:08:15",
      "check_out_time": null
    },
    "participant": {
      "id": 1,
      "identifier": "22753001",
      "name": "Ahmad Rizki Pratama",
      "study_program": "D3 Manajemen Informatika",
      "class_group": "Kompi A / Pleton 1"
    }
  }
  ```
- **Response Error (409 Conflict - Duplikat):**
  ```json
  {
    "success": false,
    "code": "DUPLICATE_SCAN",
    "message": "Peserta ini sudah melakukan absensi masuk sebelumnya pada pukul 07:08:15."
  }
  ```
- **Response Error (404 Not Found - Kartu Tidak Dikenal):**
  ```json
  {
    "success": false,
    "code": "CARD_NOT_REGISTERED",
    "message": "Kartu RFID (UID: E2999999) belum terdaftar dalam sistem."
  }
  ```

#### `GET /api/v1/participants/lookup?identifier=22753001` *(Auth: Bearer Token)*
- Mencari data peserta berdasarkan NPM/NIP atau Card UID.

---

## 7. Petunjuk Penggunaan Hardware RFID

### A. Penggunaan Web Live Scanner (Laptop / PC Petugas)
1. Login sebagai Admin atau Petugas di Web Dashboard.
2. Akses menu **Monitor Absensi** (`/attendance/monitor`).
3. Pilih sesi apel aktif hari ini.
4. Hubungkan **USB RFID Reader (13.56 MHz / 125 kHz)** ke port USB laptop/PC.
   - Reader beroperasi sebagai *USB HID Keyboard Emulation*.
   - Saat kartu ditap, reader secara otomatis mengetikkan string UID kartu dan mengirimkan karakter *Enter* (`\n`).
5. Arahkan kursor ke input tap kartu (atau sistem akan otomatis memfokuskannya).
6. Saat kartu ditempelkan:
   - Web scanner akan mengirimkan request AJAX ke backend.
   - Visual banner konfirmasi akan langsung muncul (Hijau: Hadir, Kuning: Terlambat, Merah: Ditolak/Duplikat).
   - Audio beep berbunyi untuk memberikan feedback langsung tanpa petugas harus terus menatap layar.

### B. Integrasi Controller IoT & Scanner Eksternal
Perangkat controller mandiri (seperti ESP32 / Arduino RC522) atau pemindai mobile dapat mengirimkan data presensi langsung ke endpoint REST API:
- **Endpoint:** `POST /api/v1/attendance/scan`
- **Header:** `Authorization: Bearer <TOKEN>` & `Content-Type: application/json`
- **Payload:** `{"apel_schedule_id": 1, "card_uid": "<UID_KARTU>", "scan_type": "check_in"}`

---

## 8. Desain UI & Token Visual

Sistem antarmuka web dibangun mengikuti token visual resmi pada [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/StyleGuide.md):
- **Warna Utama:** `#2D56FF` (Primary), `#4D7BFF` (Accent), `#1B37B9` (Primary Dark).
- **Warna Permukaan:** `#F4F6FC` (Background), `#FFFFFF` (Surface Card), `#D9D9D9` (Border).
- **Status Kehadiran:** `#22C55E` (Hadir), `#F5A623` (Terlambat), `#FF0004` (Alpa / Ditolak).
- **Tipografi:** *DM Sans* untuk seluruh Heading & Body, serta *IBM Plex Mono* untuk angka statistik kehadiran (`32px font-bold`).

---

## 9. Struktur Direktori Proyek

```plaintext
SiApel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/ (AuthController, AttendanceApiController)
│   │   │   ├── AttendanceController.php
│   │   │   ├── ApelScheduleController.php
│   │   │   ├── AuditLogController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── OfficerController.php
│   │   │   ├── ParticipantController.php
│   │   │   └── ReportController.php
│   │   └── Middleware/ (EnsureUserHasRole.php)
│   ├── Models/ (User, Participant, RfidCard, ApelSchedule, AttendanceRecord, AttendanceEvent, AuditLog)
│   └── Services/ (AttendanceService.php)
├── database/
│   ├── migrations/ (8 file migrasi database MySQL)
│   └── seeders/ (DatabaseSeeder, AdminSeeder, ParticipantSeeder, ApelScheduleSeeder)
├── resources/
│   ├── css/app.css (Tailwind CSS v4 token definition)
│   ├── js/app.js
│   └── views/
│       ├── layouts/ (app.blade.php, guest.blade.php)
│       └── pages/ (auth, dashboard, attendance, participants, schedules, reports, officers, audit-logs)
├── routes/
│   ├── api.php (Sanctum authenticated v1 endpoints)
│   └── web.php (Blade dashboard web routes)
├── tests/
│   ├── Feature/ (AttendanceApiTest.php, WebDashboardTest.php)
│   └── Unit/ (AttendanceServiceTest.php)
├── IMPLEMENTATION_PLAN.md           # Analisis gap, perancangan arsitektur, & status implementasi
├── PRD.md                          # Dokumen Spesifikasi Kebutuhan Produk
├── StyleGuide.md                   # Panduan token visual resmi POLINELA
└── README.md                       # Dokumentasi resmi ini
```

---

## 10. Lisensi

Hak Cipta © 2026 Politeknik Negeri Lampung (POLINELA). Seluruh hak cipta dilindungi undang-undang.
