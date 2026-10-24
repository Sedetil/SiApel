# Panduan Pengembangan untuk AI Agents (AGENTS.md)

Selamat datang di repositori **SIABSEN POLINELA** (Sistem Informasi Absensi Apel Berbasis Lokasi).  
File ini adalah instruksi operasional resmi untuk semua AI Coding Assistant (Antigravity, Claude Code, Cursor, GitHub Copilot, dsb.) dalam memahami arsitektur, standar kode, alur kerja, dan pedoman desain proyek ini.

---

## 1. Ringkasan Proyek & Konteks Domain

- **Nama Proyek:** SIABSEN POLINELA (Siabsensi-Apel)
- **Instansi:** Politeknik Negeri Lampung (POLINELA)
- **Tujuan Sistem:** Sistem informasi berbasis web untuk pencatatan dan pemantauan absensi apel mahasiswa secara real-time dengan validasi berbasis lokasi (*GPS Geofencing*).
- **Entitas & Konsep Domain Utama:**
  - **Mahasiswa:** Melakukan absensi apel mandiri dengan verifikasi koordinat lokasi perangkat (GPS).
  - **Kompi / Pleton / Regu:** Struktur pengelompokan mahasiswa dalam kegiatan apel kedisiplinan.
  - **Kegiatan Apel:** Jadwal apel pagi/sore dengan parameter titik koordinat pusat apel (*latitude*, *longitude*) dan batas radius toleransi (*radius geofence* dalam meter).
  - **Status Kehadiran:**
    - `Hadir` (Dalam radius & tepat waktu)
    - `Terlambat` (Dalam radius tetapi melewati batas waktu mulai)
    - `Izin` / `Sakit` (Disertai bukti surat pendukung)
    - `Alpa` / `Di Luar Radius` (Tidak hadir atau di luar batas toleransi GPS)
  - **Dosen / Pembina Apel / Admin:** Memantau rekap kehadiran per kompi, memvalidasi permohonan izin/sakit, dan mengekspor laporan.

---

## 2. Tech Stack & Environment

| Layer | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend** | PHP 8.3+, Laravel 13.x | Arsitektur MVC, Eloquent ORM |
| **Frontend** | Blade Templates, Vite 7.x, Tailwind CSS v4 | Server-side rendering dengan reactive asset bundling |
| **HTTP Client** | Axios 1.11+ | Request AJAX / Geolocation verification |
| **Database** | MySQL (Database: `siabsensiapel_db`) | Default port 3306, koneksi via `.env` |
| **Testing** | PHPUnit 12.x / Pest | `php artisan test` |
| **Code Style** | Laravel Pint | `vendor/bin/pint` |
| **Process Runner**| Concurrently 9.x | Menjalankan server, queue, pail, dan vite secara paralel |

---

## 3. Desain UI & Design System (Wajib Patuh)

> **PENTING:** Seluruh antarmuka **WAJIB** mengikuti spesifikasi desain yang tercantum dalam [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/Siabsensi-Apel/StyleGuide.md). Dilarang keras memakai warna acak atau arbitrary values di luar sistem token.

### Ringkasan Token Visual:
- **Warna Brand & Aksen:**
  - `Primary (Utama)`: `#2D56FF` *(Gunakan teks putih di atas Primary untuk aksi utama)*
  - `Accent`: `#4D7BFF` *(Hover, highlight, aksen)*
  - `Primary Dark`: `#1B37B9` *(Pressed state, border aktif)*
- **Warna Permukaan & Teks:**
  - `Background (Kanvas)`: `#F4F6FC`
  - `Surface (Kartu/Kontainer)`: `#FFFFFF`
  - `Border (Garis)`: `#D9D9D9`
  - `Text Primary (Teks Utama)`: `#0F1729`
  - `Text Secondary (Teks Sekunder)`: `#6B7A90`
- **Warna Semantik (Status Kehadiran):**
  - `Success (Konfirmasi / Hadir)`: `#22C55E`
  - `Warning (Perhatian / Terlambat / Pending)`: `#F5A623`
  - `Error (Validasi / Alpa / Di Luar Radius)`: `#FF0004`
- **Tipografi:**
  - Font utama: **DM Sans** (Heading, Body, Label, Caption)
  - Font metrik statistik: **IBM Plex Mono** (Stat `32px Bold` contoh: `98%`)
  - Font alternatif: **Inter**
- **Geometri Spacing (Kelipatan 4):**
  - `space-4` (4px), `space-8` (8px), `space-12` (12px), `space-16` (16px), `space-24` (24px), `space-32` (32px), `space-48` (48px), `space-64` (64px).
- **Radius Sesuai Konteks:**
  - `6px` : Elemen kecil (Badge, Tag, Pill)
  - `8px` : Elemen kontrol (Button, Input field, Select dropdown)
  - `12px` : Elemen kartu (Card, Modal dialog, Floating container)
  - `16px` : Elemen panel (Sidebar panel, Drawer sheet)
- **Aturan "Ruang yang Bernapas":**
  - `8 px` antar elemen terkait
  - `16 px` di dalam kontrol
  - `24 px` di dalam kartu
  - `32 px` antar kelompok

---

## 4. Struktur Direktori Proyek

```plaintext
Siabsensi-Apel/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controller alur absensi, kompi, dan manajemen apel
│   │   ├── Middleware/         # Auth, Role, GPS restriction middleware
│   │   └── Requests/           # Form validation requests (StoreAbsensiRequest, dsb.)
│   ├── Models/                 # Eloquent Models (User, Kompi, JadwalApel, Absensi, dsb.)
│   └── Providers/
├── config/                     # Konfigurasi aplikasi
├── database/
│   ├── factories/              # Database factories untuk testing
│   ├── migrations/             # Skema tabel database (Users, Kompi, Absensi, dsb.)
│   └── seeders/                # Database seeders (UserSeeder, KompiSeeder, dsb.)
├── public/                     # Entrypoint publik & compiled assets
├── resources/
│   ├── css/
│   │   └── app.css             # Import Tailwind CSS v4 & @theme visual tokens
│   ├── js/
│   │   ├── app.js              # Entrypoint script JS & Axios
│   │   └── geolocation.js      # Modul GPS Geofencing HTML5 Geolocation API
│   └── views/
│       ├── components/         # Blade reusable components (stat-card, badge, button, dsb.)
│       ├── layouts/            # Layout utama (app.blade.php, guest.blade.php)
│       └── pages/              # View halaman (dashboard, absensi, kompi, laporan)
├── routes/
│   ├── web.php                 # Routing halaman web & dashboard
│   └── console.php             # Artisan console commands
├── storage/                    # File uploads (bukti surat sakit/izin, log aplikasi)
├── tests/                      # Unit & Feature automated tests
├── StyleGuide.md               # Spesifikasi lengkap Design System
├── AGENTS.md                   # Dokumen pedoman ini
├── package.json                # Dependencies Frontend & scripts Vite
└── composer.json               # Dependencies Backend Laravel
```

---

## 5. Perintah Pengembangan (Development Commands)

### Menjalankan Lingkungan Lokal
```bash
# Menjalankan seluruh stack sekaligus (Server + Vite + Queue + Logs):
composer run dev

# ATAU menjalankan secara individual di terminal terpisah:
php artisan serve            # Menjalankan PHP development server (http://127.0.0.1:8000)
npm run dev                  # Menjalankan Vite live-reloading bundler
```

### Database & Migrasi
```bash
php artisan migrate                  # Menjalankan migrasi database baru
php artisan migrate:status           # Mengecek status migrasi
php artisan db:seed                  # Menjalankan seeder data awal
php artisan make:migration <nama>    # Membuat file migrasi baru
```

### Testing & Code Quality
```bash
# Menjalankan automated tests:
php artisan test
# atau
composer test

# Memformat kode sesuai standar PSR-12 / Laravel:
vendor/bin/pint
```

### Build Aset Produksi
```bash
npm run build
```

---

## 6. Konvensi Koding & Standar Arsitektur

### Backend (PHP / Laravel)
1. **Model & Database:**
   - Gunakan nama Model singular PascalCase (contoh: `Absensi`, `Kompi`, `JadwalApel`).
   - Selalu definisikan `$fillable` atau `$guarded`, dan relasi Eloquent yang eksplisit dengan *return type hints* (contoh: `public function kompi(): BelongsTo`).
   - Jangan menulis kueri SQL mentah jika dapat diselesaikan secara bersih dengan Eloquent atau Query Builder.
2. **Controller & Business Logic:**
   - Pertahankan Controller tetap ramping (*thin controllers*). Pindahkan validasi ke Form Request (`php artisan make:request StoreAbsensiRequest`).
   - Logika kalkulasi jarak geofence (Haversine formula / Vincenty) sebaiknya diletakkan di Service Class terpisah (misal: `App\Services\GeolocationService`).
3. **Keamanan & Validasi:**
   - Validasi data GPS di sisi server (*server-side verification*) selain di browser, termasuk verifikasi waktu pengiriman agar tidak dapat dipalsukan.
   - Selalu gunakan CSRF token (`@csrf`) pada form Blade.

### Frontend (Blade & Tailwind CSS)
1. **Pemanfaatan Komponen Blade:**
   - Buat komponen modular di `resources/views/components/` untuk elemen yang berulang (misal: `<x-stat-card>`, `<x-status-badge>`, `<x-button>`).
2. **Penamaan Class & Styling:**
   - Utamakan penggunaan class Tailwind yang selaras dengan `StyleGuide.md`.
   - Hindari hardcoded inline styles (`style="..."`) jika sudah tersedia utilitas Tailwind.
   - Mobile-Friendly: Halaman presensi mahasiswa harus dioptimalkan untuk tampilan layar smartphone (layar sentuh, tombol besar minimum tap target 44px, status radius jelas).

### Standar Bahasa & Terminologi
- Gunakan Bahasa Indonesia formal dan baku untuk antarmuka pengguna (UI), label, status, dan pesan error/sukses (contoh: *"Absensi Berhasil Disimpan"*, *"Di Luar Jangkauan Lokasi Apel"*).
- Kode program (nama fungsi, variabel, nama kolom database) menggunakan Bahasa Inggris atau istilah teknis yang konsisten (contoh: `latitude`, `longitude`, `attendance_status`, `check_in_time`).

---

## 7. Aturan Khusus untuk AI Agents

1. **Jaga Integritas Data & Keamanan:**
   - JANGAN PERNAH menjalankan `migrate:fresh` atau `db:wipe` di terminal tanpa instruksi eksplisit dari pengguna, untuk mencegah kehilangan data uji/lokal.
   - JANGAN memasukkan atau mengekspos credential rahasia di dalam commit atau file publik. Hormati konfigurasi di `.env`.
2. **Konsistensi Visual:**
   - Setiap membuat atau memodifikasi file Blade, cek kembali token warna dan tipografi di [StyleGuide.md](file:///d:/Project%20Web%20dan%20Tugas/Siabsensi-Apel/StyleGuide.md).
   - Pastikan hierarki teks menggunakan font yang sesuai (`IBM Plex Mono` untuk angka statistik kehadiran, `DM Sans` untuk heading dan body).
3. **Penyajian Kode & Tanggapan:**
   - Buat perubahan kode yang presisi, minimalkan *breaking changes*.
   - Pertahankan komentar kode yang sudah ada.
   - Sertakan tautan file markdown yang dapat diklik (format `[nama_file](file:///path/to/file)`) saat merujuk ke file yang dibuat atau diubah.
