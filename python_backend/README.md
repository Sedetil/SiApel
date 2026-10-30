# SIABSEN POLINELA — Sistem Presensi Alat Scanner QR Lokal (Python)

Modul ini menjalankan sistem presensi apel lokal menggunakan **Alat Scanner QR (USB HID Keyboard Emulation / Web Serial COM)** yang tersimpan di database **MySQL Lokal** serta memiliki fitur **Backup Excel Lokal** dan **Push Sinkronisasi ke Database Server Online**.

> **Catatan:** Modul ini tidak menggunakan YOLO ataupun CCTV, melainkan didedikasikan penuh untuk kecepatan pemindaian **Alat Scanner QR Fisik**.

---

## Cara Menjalankan

### 1. Instalasi Dependency Python
```bash
cd python_backend
pip install -r requirements.txt
```

### 2. Jalankan Server Python Lokal
```bash
cd python_backend
python api_server.py
```
Server akan aktif di `http://127.0.0.1:5000`.

### 3. Buka Halaman Monitor Scanner QR Lokal
Buka browser ke:
- **URL Monitor:** `http://127.0.0.1:5000/monitor`

---

## Alur Kerja (Lokal ke Online)

1. **Tarik Master Data (`Sync Master Data`)**
   - Klik tombol **Sync Master Data** di halaman monitor.
   - Masukkan alamat URL Server Laravel (misalnya `http://127.0.0.1:8000` atau URL hosting online).
   - Data peserta/mahasiswa beserta jadwal apel dan batas waktu keterlambatan akan disalin ke MySQL lokal.

2. **Pemindaian Presensi dengan Alat Scanner QR**
   - Tembakkan Alat Scanner QR ke kartu/kode QR mahasiswa.
   - Sistem langsung membaca kode QR secara otomatis (mode USB Keyboard Emulation maupun Serial COM), memvalidasi jadwal apel & keterlambatan di lokal, membunyikan indikator suara *beep*, dan mencatatnya ke antrean lokal.

3. **Push ke Database Server Online (`Sync ke Server`)**
   - Klik tombol **Sync ke Server** di panel sebelah kanan.
   - Sistem otomatis membuat file backup Excel di `python_backend/backups/` terlebih dahulu, kemudian mengirim seluruh data presensi yang belum disinkron ke endpoint `POST /api/sync` pada server Laravel online.

