# IMPLEMENTATION PLAN: Sistem Absensi Apel Berbasis RFID (SIABSEN POLINELA)

- **Dokumen Referensi:** [PRD.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/PRD.md)
- **Design System:** [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/StyleGuide.md) & [AGENTS.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/AGENTS.md)
- **Status Dokumen:** Rencana Kerja Eksekusi & Analisis Gap
- **Tanggal:** 22 Oktober 2026

---

## 1. Analisis Gap & Kondisi Proyek

### 1.1 Kondisi Repositori Saat Ini
- **Backend:** Laravel 13.x, PHP 8.4.24, MySQL 8.0.30 (`siapel_db`).
- **Frontend Web:** Blade Template, Vite 7.x, Tailwind CSS v4 dengan konfigurasi `@theme` di `resources/css/app.css` (sudah selaras dengan token warna `StyleGuide.md`).
- **Database:** Tabel default Laravel sudah termigrasi (`users`, `sessions`, `jobs`, `cache`), namun belum ada entitas bisnis absensi apel dan RFID.
- **Autentikasi & Otorisasi:** Controller auth belum diimplementasikan, rute login masih berupa closure mockup, belum ada pemisahan peran `admin` dan `petugas`.
- **API & Integrasi Android:** Belum ada rute `routes/api.php` dan Laravel Sanctum belum dikonfigurasi. Belum ada folder proyek aplikasi Android.

### 1.2 Matriks Kebutuhan PRD vs Ketersediaan

| Kode PRD | Deskripsi Kebutuhan | Prioritas | Status Repositori | Rencana Aksi |
|---|---|---|---|---|
| **FR-01** | Autentikasi & Sesi (Admin & Petugas) | P0 | Belum Ada | Tambah role & status aktif pada `users`, buat `AuthController` untuk web (session) dan API (Sanctum/token). |
| **FR-02** | Manajemen Data Peserta | P0 | Belum Ada | Buat model/tabel `participants`, CRUD peserta di dashboard web dengan soft-deactivation. |
| **FR-03** | Registrasi & Pemetaan Kartu RFID | P0 | Belum Ada | Buat model/tabel `rfid_cards`, validasi 1 kartu aktif ke 1 peserta aktif, pencegahan UID ganda. |
| **FR-04** | Manajemen Jadwal Apel & Aturan Keterlambatan | P0 | Belum Ada | Buat model/tabel `apel_schedules`, setting tanggal, jam mulai, jam selesai, toleransi terlambat. |
| **FR-05** | Absensi Masuk (Validasi Backend & Anti-duplikat) | P0 | Belum Ada | Buat `AttendanceService`, tabel `attendance_records` & `attendance_events`, logic validasi keterlambatan berbasis waktu server. |
| **FR-06** | Absensi Keluar | P0 | Belum Ada | Dukung pencatatan `check_out_time` pada record absensi yang sudah memiliki `check_in`. |
| **FR-07** | Riwayat Absensi per Peserta & Sesi | P0 | Belum Ada | Tampilan riwayat tabular dengan filter lengkap dan pagination. |
| **FR-08** | Dashboard Statistik | P0 | Belum Ada | Tampilan KPI ringkasan kehadiran (font `IBM Plex Mono` sesuai StyleGuide), persentase, chart, filter sesi. |
| **FR-09** | Rekap & Laporan | P0 | Belum Ada | Halaman laporan per jadwal/rentang tanggal dengan pengelompokan hadir, terlambat, belum tercatat. |
| **FR-10** | Ekspor Excel (CSV/XLSX) & PDF | P1 | Belum Ada | Service ekspor laporan tabular format CSV/XLSX dan PDF/Print view yang rapi. |
| **FR-11** | Aplikasi Android Petugas | P0 | Belum Ada | Proyek Android (Kotlin/Gradle) dengan antarmuka Login, Pilih Sesi, Pemindaian RFID & Umpan Balik Status real-time. |
| **FR-12** | Integrasi RFID Reader & API Backend | P0 | Belum Ada | Endpoint API seragam (`POST /api/v1/attendance/scan`), dukungan input HID/USB/NFC/REST, serta halaman simulator reader di web. |
| **FR-13** | Audit Log | P1 | Belum Ada | Model `AuditLog` dan middleware/helper untuk mencatat aktivitas penting (mutasi data, pemetaan kartu, login). |
| **FR-14** | Manajemen Akun Petugas | P0 | Belum Ada | CRUD akun petugas oleh admin, pembatasan akses hanya ke sesi apel. |

---

## 2. Rancangan Basis Data (Database Schema)

### 2.1 Tabel `users` (Pembaruan)
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `name` (VARCHAR 255)
- `username` (VARCHAR 100, UNIQUE)
- `email` (VARCHAR 255, UNIQUE, NULLABLE)
- `password` (VARCHAR 255)
- `role` (ENUM: `'admin'`, `'petugas'`)
- `is_active` (BOOLEAN, DEFAULT true)
- `remember_token` (VARCHAR 100, NULLABLE)
- `created_at`, `updated_at`

### 2.2 Tabel `participants`
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `identifier` (VARCHAR 64, UNIQUE) — NPM / NIM peserta
- `name` (VARCHAR 255)
- `study_program` (VARCHAR 100) — Program Studi / Jurusan
- `class_group` (VARCHAR 50) — Kompi / Pleton / Kelas
- `is_active` (BOOLEAN, DEFAULT true)
- `created_at`, `updated_at`, `deleted_at` (soft delete)

### 2.3 Tabel `rfid_cards`
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `participant_id` (BIGINT, FK -> `participants.id`)
- `card_uid` (VARCHAR 64, INDEX) — UID RFID (HEX / Desimal)
- `is_active` (BOOLEAN, DEFAULT true)
- `issued_at` (TIMESTAMP)
- `deactivated_at` (TIMESTAMP, NULLABLE)
- `notes` (TEXT, NULLABLE)
- `created_at`, `updated_at`
- *Constraint:* Unik kartu aktif (`card_uid` + `is_active = 1`) untuk mencegah 1 kartu digunakan banyak peserta.

### 2.4 Tabel `apel_schedules`
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `title` (VARCHAR 255) — Misal: *"Apel Kedisiplinan Pagi"*
- `schedule_date` (DATE)
- `start_time` (TIME) — Waktu apel dimulai
- `end_time` (TIME) — Batas akhir sesi apel
- `attendance_start` (TIME) — Periode awal dibukanya tap absensi
- `attendance_end` (TIME) — Periode akhir penutupan tap absensi
- `late_threshold_time` (TIME) — Batas waktu tepat waktu (setelah ini status: `terlambat`)
- `location` (VARCHAR 255, DEFAULT 'Lapangan Apel Kampus')
- `status` (ENUM: `'scheduled'`, `'active'`, `'completed'`, `'cancelled'`)
- `created_by` (BIGINT, FK -> `users.id`)
- `created_at`, `updated_at`

### 2.5 Tabel `attendance_records`
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `apel_schedule_id` (BIGINT, FK -> `apel_schedules.id`, INDEX)
- `participant_id` (BIGINT, FK -> `participants.id`, INDEX)
- `rfid_card_id` (BIGINT, FK -> `rfid_cards.id`, NULLABLE)
- `check_in_time` (TIMESTAMP, NULLABLE)
- `check_out_time` (TIMESTAMP, NULLABLE)
- `status` (ENUM: `'hadir'`, `'terlambat'`)
- `recorded_by` (BIGINT, FK -> `users.id`, NULLABLE) — Petugas pelaksana
- `device_identifier` (VARCHAR 100, NULLABLE)
- `notes` (VARCHAR 255, NULLABLE)
- `created_at`, `updated_at`
- *Constraint:* UNIQUE (`apel_schedule_id`, `participant_id`) untuk absensi masuk (anti-duplikasi mutlak).

### 2.6 Tabel `attendance_events` (Audit Jejak Tap RFID)
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `apel_schedule_id` (BIGINT, FK -> `apel_schedules.id`, NULLABLE)
- `card_uid` (VARCHAR 64)
- `participant_id` (BIGINT, FK -> `participants.id`, NULLABLE)
- `scan_type` (ENUM: `'check_in'`, `'check_out'`)
- `scanned_at` (TIMESTAMP)
- `result` (ENUM: `'success'`, `'rejected'`)
- `rejection_reason` (VARCHAR 100, NULLABLE) — e.g. `card_not_registered`, `participant_inactive`, `schedule_closed`, `duplicate_scan`
- `device_identifier` (VARCHAR 100, NULLABLE)
- `created_at`

### 2.7 Tabel `audit_logs`
- `id` (BIGINT, PK, AUTO_INCREMENT)
- `user_id` (BIGINT, FK -> `users.id`, NULLABLE)
- `action` (VARCHAR 100) — e.g. `CREATE_PARTICIPANT`, `MAP_RFID`, `UPDATE_SCHEDULE`
- `entity_type` (VARCHAR 100, NULLABLE)
- `entity_id` (BIGINT, NULLABLE)
- `description` (TEXT)
- `ip_address` (VARCHAR 45, NULLABLE)
- `user_agent` (VARCHAR 255, NULLABLE)
- `created_at`

---

## 3. Arsitektur API (RESTful untuk Android & RFID Reader)

Semua endpoint dilindungi validasi backend, rate limiting, dan autentikasi token (Laravel Sanctum).

### 3.1 Endpoint Autentikasi
- `POST /api/v1/auth/login` — Login petugas/admin via kredensial (NPM/Email/Username & Password), return Sanctum Token & user object.
- `GET /api/v1/auth/me` — Info profil pengguna login.
- `POST /api/v1/auth/logout` — Revoke token aktif.

### 3.2 Endpoint Operasional Petugas & RFID
- `GET /api/v1/schedules/active` — Mendapatkan daftar jadwal apel yang berstatus aktif atau hari ini.
- `GET /api/v1/schedules/{id}/summary` — Ringkasan metrik kehadiran sesi (Total Hadir, Terlambat, Scan Terakhir).
- `POST /api/v1/attendance/scan` — Endpoint utama pemrosesan kartu RFID.
  - **Payload:**
    ```json
    {
      "apel_schedule_id": 1,
      "card_uid": "3A8F9B12",
      "scan_type": "check_in",
      "device_identifier": "android-petugas-01"
    }
    ```
  - **Aturan Validasi Server:**
    1. Periksa apakah jadwal apel aktif dan dalam periode waktu absensi (`attendance_start` s.d. `attendance_end`).
    2. Cari kartu di `rfid_cards` dengan status `is_active = true`. Jika tidak ada -> return HTTP 422: `KARTU_TIDAK_DIKENAL`.
    3. Periksa status `participant`. Jika `is_active = false` -> return HTTP 422: `PESERTA_NONAKTIF`.
    4. Cek apakah peserta sudah tap masuk untuk jadwal ini (`attendance_records` where schedule_id & participant_id). Jika sudah ada -> return HTTP 409: `DUPLIKAT_ABSENSI` (beserta data rekaman sebelumnya).
    5. Hitung keterlambatan menggunakan waktu backend (`now()`):
       - Jika `now() <= late_threshold_time` -> status `hadir`.
       - Jika `now() > late_threshold_time` -> status `terlambat`.
    6. Gunakan Database Transaction untuk menyimpan `attendance_records` dan mencatat `attendance_events`.
    7. Kembalikan response JSON terstruktur yang memuat status, nama peserta, nomor induk, status kehadiran, dan waktu absensi.

---

## 4. Rencana Antarmuka Web Dashboard (Design System Compliant)

Mengikuti [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/SiApel/StyleGuide.md):
- **Warna:** Primary `#2D56FF`, Accent `#4D7BFF`, Dark `#1B37B9`, Kanvas `#F4F6FC`, Surface `#FFFFFF`, Border `#D9D9D9`.
- **Status Semantik:** Success `#22C55E` (Hadir), Warning `#F5A623` (Terlambat), Error `#FF0004` (Ditolak/Alpa).
- **Tipografi:** DM Sans (Heading, Body), IBM Plex Mono (Statistik Metrik 32px Bold).
- **Radius:** 6px (Badge), 8px (Button/Input), 12px (Card/Modal), 16px (Panel).

### Daftar Tampilan Web:
1. `pages/auth/login.blade.php`: Halaman login admin/petugas yang aman dan responsif.
2. `pages/dashboard.blade.php`: Ringkasan statistik kehadiran, jadwal apel aktif, chart/visualisasi tingkat kehadiran apel.
3. `pages/participants/index.blade.php`: Tabel data peserta, filter prodi, status kartu RFID, tombol tambah/edit/nonaktifkan.
4. `pages/participants/rfid.blade.php`: Modal / antarmuka pendaftaran dan pemetaan UID kartu RFID ke peserta.
5. `pages/schedules/index.blade.php`: Daftar jadwal apel, konfigurasi jam mulai, jam selesai, toleransi terlambat.
6. `pages/attendance/monitor.blade.php`: Live scanner & attendance monitor untuk administrasi apel langsung melalui dashboard (mendukung reader USB HID barcode/RFID).
7. `pages/reports/index.blade.php`: Rekap kehadiran lengkap dengan filter multi-parameter.
8. `pages/reports/export.blade.php`: Ekspor data ke format CSV/XLSX dan PDF/Print view resmi.
9. `pages/officers/index.blade.php`: Pengelolaan akun petugas apel.
10. `pages/audit-logs/index.blade.php`: Tampilan audit log aktivitas sistem.

---

## 5. Rencana Arsitektur Aplikasi Android Petugas (`android/`)

- **Bahasa:** Kotlin (Android Native standard)
- **Networking:** Retrofit 2 + OkHttp 3 + Kotlinx Serialization
- **Arsitektur UI:** Material 3, MVVM / Clean Android Architecture
- **Fitur Utama:**
  1. **Login Petugas:** Autentikasi ke backend Laravel menggunakan REST API.
  2. **Pemilihan Sesi Apel:** Menampilkan jadwal apel yang aktif hari ini.
  3. **Pemindaian RFID / NFC:**
     - Menangkap masukan reader (HID USB / Bluetooth / NFC Intent / Scanner Input).
     - Menampilkan feedback instan dengan visual kontras: Hijau (Hadir Tepat Waktu), Kuning (Terlambat), Merah (Ditolak / Kartu Tidak Dikenal / Duplikat).
  4. **Ringkasan Sesi:** Rekap real-time jumlah peserta yang telah hadir vs terlambat.

---

## 6. Tahapan Implementasi Terperinci

### Tahap 1: Analisis, Model Data, & Migrasi Database
- [x] Analisis repository & PRD gap analysis.
- [x] Buat file migrasi database:
  - Update `users` (tambah role, is_active, username).
  - Buat `participants`, `rfid_cards`, `apel_schedules`, `attendance_records`, `attendance_events`, `audit_logs`.
- [x] Buat Eloquent Models lengkap dengan relasi, fillable, type casting, dan scope query.
- [x] Buat Seeder (`DatabaseSeeder`, `AdminSeeder`, `ParticipantSeeder`, `ApelScheduleSeeder`).

### Tahap 2: Autentikasi, Otorisasi, & Backend Services
- [x] Konfigurasi Laravel Sanctum dan middleware role (`EnsureUserHasRole`).
- [x] Implementasikan `AuthController` (Web Session & API Token).
- [x] Buat `AttendanceService`:
  - Validasi ketat aturan waktu server (`now()`).
  - Cek kartu aktif, peserta aktif, sesi aktif.
  - Logika anti-duplikasi idempoten dengan DB Transaction & Unique constraints.
  - Pencatatan log event tap kartu ke `attendance_events`.
- [x] Buat audit trail untuk pencatatan mutasi dan aktivitas.

### Tahap 3: REST API untuk Android & RFID Reader
- [x] Daftarkan rute API di `routes/api.php` dan register di `bootstrap/app.php`.
- [x] Buat `Api\AttendanceApiController`:
  - `POST /api/v1/attendance/scan`
  - `GET /api/v1/schedules/active`
  - `GET /api/v1/schedules/{id}/summary`
  - `GET /api/v1/participants/lookup`
- [x] Buat automated feature test untuk seluruh endpoint API (`AttendanceApiTest.php`).

### Tahap 4: Web Dashboard Admin (Blade, Tailwind CSS v4, Components)
- [x] Buat komponen Blade dan view sesuai `StyleGuide.md` (Warna `#2D56FF`, font DM Sans & IBM Plex Mono 32px):
  - Halaman Login Web (`/login`)
  - Halaman Dashboard Admin statistik dan KPI ringkasan (`/dashboard`).
  - Halaman CRUD Peserta & Pemetaan Kartu RFID (`/participants`).
  - Halaman CRUD Jadwal Apel & Aturan Keterlambatan (`/schedules`).
  - Halaman Live Monitor Absensi Apel dengan audio Web Audio feedback (`/attendance/monitor`).
  - Halaman Riwayat Absensi tabular (`/attendance/history`).
  - Halaman Manajemen Akun Petugas (`/officers`).
  - Halaman Audit Log (`/audit-logs`).

### Tahap 5: Rekap Laporan & Ekspor (Excel & PDF)
- [x] Halaman filter rekap kehadiran berdasarkan tanggal, jadwal, prodi, dan status (`/reports`).
- [x] Ekspor ke CSV/Excel dengan header lengkap (`/reports/export-csv`).
- [x] Cetak PDF/Print view yang bersih dan siap dicetak (`/reports/export-pdf`).

### Tahap 6: Aplikasi Android Petugas
- [x] Buat struktur proyek native Android di direktori `android/` lengkap dengan Gradle wrapper, manifest, source code Kotlin, Material 3, layout XML.
- [x] Implementasikan API Client Retrofit, Data Model, UI Screen (LoginActivity, SessionActivity, ScanActivity).
- [x] Dukung integrasi input scanner reader (Keyboard HID mode / USB OTG / NFC reader bawaan) dan audio tone feedback.

### Tahap 7: Pengujian, Keamanan, & Verifikasi
- [x] Jalankan automated test suite (`php vendor/phpunit/phpunit/phpunit`): 28 tests, 76 assertions, 100% lulus.
- [x] Jalankan code formatter `vendor/bin/pint` standar PSR-12.
- [x] Jalankan kompilasi frontend `npm run build` dengan Vite v7 + Tailwind CSS v4.
- [x] Validasi keamanan: otorisasi role, pencegahan IDOR, SQL injection, CSRF, input sanitization.
- [x] Uji skenario batas waktu absensi (sebelum, tepat pada batas, dan setelah batas keterlambatan).
- [x] Uji anti-duplikasi pemindaian kartu berulang.

### Tahap 8: Dokumentasi & Panduan Operasional
- [x] Perbarui `README.md` dengan instruksi setup database, migrasi, dan cara menjalankan sistem.
- [x] Dokumentasi endpoint API lengkap di `README.md`.
- [x] Buat panduan konfigurasi perangkat RFID reader (Web & Android).

---

## 7. Hal yang Memerlukan Konfirmasi (`[PERLU DIKONFIRMASI]`)

1. **Model Hardware RFID:** Reader USB HID (keyboard wedge emulator), USB Serial, Bluetooth, atau NFC smartphone Android bawaan?
   - *Solusi Desain Antigravity:* Sistem dibuat fleksibel; API backend menerima string UID universal (`card_uid`), baik dari Android (NFC/USB), reader USB PC, maupun controller IoT.
2. **Kewajiban Absensi Keluar:** Apakah absensi keluar wajib untuk seluruh peserta?
   - *Solusi Desain Antigravity:* Kolom `check_out_time` disediakan di database. Status kehadiran utama dihitung dari absensi masuk, sehingga absensi keluar bersifat pelengkap yang dapat diaktifkan sesuai kebutuhan operasional.
3. **Format Ekspor Laporan:** Format CSV/Excel dan format cetak dokumen resmi (Print-ready HTML/PDF).
