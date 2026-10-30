# PRD — Sistem Absensi Apel Berbasis RFID

- **Versi dokumen:** 1.0
- **Status:** Draft kebutuhan produk
- **Bahasa:** Indonesia
- **Platform:** Web dashboard, aplikasi Android, perangkat RFID
- **Backend:** Laravel
- **Database:** MySQL

> **Catatan penting:** PRD ini memuat kebutuhan yang sudah disepakati dan menandai keputusan yang belum ditentukan sebagai **[PERLU DIKONFIRMASI]**. Jangan menganggap detail perangkat, protokol komunikasi, maupun integrasi sebagai keputusan final sebelum dikonfirmasi.

---

## 1. Ringkasan Produk

Sistem Absensi Apel Berbasis RFID adalah sistem untuk mengelola peserta dan jadwal apel kampus serta mencatat kehadiran menggunakan kartu/tag RFID. Sistem menyediakan dashboard web untuk administrasi dan pelaporan, serta aplikasi Android untuk kebutuhan operasional petugas apel.

Waktu absensi ditentukan oleh admin. Sistem membandingkan waktu pemindaian kartu dengan jadwal dan aturan waktu yang telah dikonfigurasi untuk menentukan status kehadiran, termasuk keterlambatan.

### 1.1 Tujuan

1. Mempercepat dan menertibkan pencatatan kehadiran apel.
2. Mengurangi pencatatan manual dan kesalahan rekap.
3. Menyediakan riwayat kehadiran per peserta.
4. Memudahkan pengelolaan jadwal apel dan batas waktu absensi.
5. Menyediakan statistik dan laporan yang dapat diekspor ke Excel atau PDF.
6. Menyediakan pembagian fungsi yang jelas antara admin dan petugas apel.

### 1.2 Pernyataan Masalah

Pencatatan kehadiran secara manual dapat memerlukan waktu, menyulitkan rekap, dan meningkatkan risiko kesalahan input. Sistem ini bertujuan memusatkan data peserta, jadwal, pemindaian RFID, dan laporan dalam satu alur kerja.

### 1.3 Target Pengguna

- **Admin:** mengelola data, jadwal, konfigurasi absensi, akun petugas, dan laporan.
- **Petugas apel:** menjalankan proses absensi pada jadwal yang ditugaskan dan memantau hasil pemindaian.

Peran tambahan seperti peserta dengan akun mandiri, dosen/pimpinan, atau operator tingkat program studi **belum termasuk** dan perlu persetujuan tersendiri.

---

## 2. Ruang Lingkup Produk

### 2.1 Termasuk dalam cakupan (In Scope)

- Login dan kontrol akses untuk admin dan petugas apel.
- Pengelolaan data peserta kampus.
- Pendaftaran/pemetaan UID atau identitas kartu RFID ke peserta, sesuai kemampuan perangkat yang dipilih.
- Pengelolaan jadwal apel.
- Pengaturan periode absensi dan batas keterlambatan.
- Pencatatan absensi masuk dan keluar.
- Validasi pemindaian kartu dan pencegahan pencatatan ganda sesuai aturan.
- Penentuan status hadir/terlambat berdasarkan waktu yang dikonfigurasi.
- Riwayat absensi per peserta dan per jadwal apel.
- Dashboard statistik.
- Filter laporan dan ekspor Excel/PDF.
- Validasi, penanganan kesalahan, dan pencatatan aktivitas penting.

### 2.2 Di luar cakupan untuk versi awal (Out of Scope)

Hal-hal berikut tidak dibuat kecuali disetujui kemudian:

- Pengenalan wajah, sidik jari, GPS, atau biometrik.
- Penggajian, penilaian akademik, atau integrasi sistem akademik kampus.
- Notifikasi SMS/WhatsApp/push.
- Portal mandiri peserta atau akun mahasiswa.
- Pengadaan perangkat keras, instalasi jaringan, dan desain casing.
- Sinkronisasi offline penuh antarperangkat.
- Penetapan sanksi otomatis atas keterlambatan atau ketidakhadiran.
- Integrasi dengan sistem identitas atau SSO kampus.

---

## 3. Pemangku Kepentingan dan Peran

### 3.1 Admin

Admin berwenang untuk:

- Masuk dan keluar dari sistem.
- Membuat, melihat, mengubah, mengarsipkan, dan mengimpor data peserta sesuai kebijakan.
- Mengelola identitas kartu RFID peserta.
- Membuat, mengubah, mengaktifkan, membatalkan, dan melihat jadwal apel.
- Menentukan waktu mulai dan berakhirnya absensi serta batas keterlambatan.
- Mengelola akun petugas apel dan status aktifnya.
- Melihat seluruh rekap, riwayat, dan statistik yang menjadi kewenangannya.
- Mengekspor laporan.
- Mengoreksi catatan absensi dengan alasan yang wajib dicatat, apabila fitur koreksi disetujui.
- Melihat audit log aktivitas penting.

### 3.2 Petugas Apel

Petugas apel berwenang untuk:

- Masuk dan keluar dari sistem.
- Melihat jadwal apel yang ditugaskan atau tersedia sesuai konfigurasi.
- Memilih/memulai sesi absensi yang aktif.
- Memantau hasil pemindaian kartu.
- Melakukan pemindaian RFID untuk absensi masuk dan keluar.
- Melihat ringkasan hasil sesi yang menjadi kewenangannya.

Petugas tidak boleh mengubah data peserta, jadwal, aturan keterlambatan, atau menghapus catatan absensi, kecuali izin khusus diberikan secara eksplisit.

### 3.3 Matriks Hak Akses

| Fungsi | Admin | Petugas apel |
|---|---|---|
| Kelola data peserta | Ya | Tidak |
| Kelola kartu RFID | Ya | Tidak, kecuali proses registrasi ditugaskan |
| Kelola jadwal dan aturan waktu | Ya | Tidak |
| Menjalankan absensi | Ya, jika diperlukan | Ya |
| Melihat riwayat semua peserta | Ya | Terbatas pada sesi/tugas |
| Melihat statistik global | Ya | Ringkasan sesi sesuai kewenangan |
| Ekspor laporan | Ya | Tidak secara default |
| Koreksi catatan absensi | Sesuai kebijakan dan audit | Tidak secara default |
| Kelola akun petugas | Ya | Tidak |
| Lihat audit log | Ya | Tidak |

---

## 4. Kebutuhan Fungsional

Prioritas: **P0** = wajib untuk versi awal; **P1** = penting, dapat menyusul setelah alur inti stabil.

### FR-01 — Autentikasi dan Sesi

- **Prioritas:** P0
- Sistem harus menyediakan login dan logout.
- Kredensial harus diverifikasi oleh backend.
- Halaman dan endpoint harus memeriksa autentikasi serta peran pengguna.
- Akun yang dinonaktifkan tidak dapat memulai sesi baru.
- Pesan kesalahan tidak boleh membocorkan apakah kata sandi yang salah atau akun tertentu yang terdaftar.

### FR-02 — Manajemen Data Peserta

- **Prioritas:** P0
- Admin dapat menambah, melihat, mengubah, dan menonaktifkan/mengarsipkan peserta.
- Data minimum yang diperlukan: nomor induk peserta, nama, status aktif, dan identitas kartu RFID yang terhubung.
- Program studi, kelas, angkatan, fakultas, atau unit dapat ditambahkan jika dibutuhkan.
- Nomor induk harus unik dalam lingkup yang disepakati.
- Satu kartu aktif tidak boleh terhubung ke lebih dari satu peserta pada waktu yang sama.
- Peserta yang dinonaktifkan tidak boleh menghasilkan absensi baru, kecuali aturan khusus disetujui.
- Penghapusan permanen tidak menjadi pilihan default agar riwayat tetap terjaga.

**[PERLU DIKONFIRMASI]** Kolom wajib peserta, format nomor induk, impor data dari Excel, dan apakah satu peserta dapat memiliki beberapa kartu.

### FR-03 — Pendaftaran dan Pemetaan Kartu RFID

- **Prioritas:** P0
- Admin dapat memetakan identitas kartu ke satu peserta.
- Sistem harus menolak identitas kartu yang sudah dipetakan ke peserta lain.
- Admin dapat menonaktifkan atau mengganti pemetaan kartu.
- Sistem tidak boleh mengandalkan nama yang dikirim perangkat sebagai identitas utama; pemetaan harus dilakukan menggunakan identitas kartu yang telah terdaftar.
- Identitas kartu disimpan dan diproses sesuai kemampuan reader dan kebijakan keamanan perangkat.

**[PERLU DIKONFIRMASI]** Jenis kartu, frekuensi, reader, format UID yang diterima, serta metode reader mengirimkan data ke backend.

### FR-04 — Manajemen Jadwal Apel

- **Prioritas:** P0
- Admin dapat membuat, melihat, mengubah, dan membatalkan jadwal apel.
- Data jadwal minimum: nama/judul, tanggal, waktu mulai, waktu akhir, dan status.
- Admin dapat menentukan awal dan akhir periode absensi serta batas waktu yang digunakan untuk menandai keterlambatan.
- Sistem harus memvalidasi bahwa waktu akhir tidak lebih awal dari waktu mulai.
- Jadwal yang telah memiliki catatan absensi tidak boleh dihapus permanen tanpa prosedur khusus.
- Jadwal harus memiliki zona waktu yang konsisten; untuk kampus di Indonesia, zona waktu operasional perlu ditetapkan secara eksplisit.

**[PERLU DIKONFIRMASI]** Apakah jadwal berulang (misalnya setiap Senin), lokasi, kelompok peserta, dan penugasan petugas diperlukan.

### FR-05 — Absensi Masuk

- **Prioritas:** P0
- Petugas memilih atau memulai jadwal/sesi yang valid.
- Saat kartu dibaca, sistem memeriksa apakah kartu terdaftar dan aktif.
- Sistem memeriksa status peserta, sesi, periode absensi, dan apakah pemindaian merupakan duplikasi.
- Waktu pencatatan ditentukan oleh waktu tepercaya dari backend, kecuali arsitektur perangkat memerlukan mekanisme lain yang disepakati.
- Sistem menentukan status hadir atau terlambat berdasarkan aturan jadwal.
- Hasil pemindaian harus memberikan umpan balik yang jelas: berhasil, terlambat, kartu tidak dikenal, peserta nonaktif, sesi tidak aktif, di luar periode absensi, atau duplikat.
- Satu peserta hanya memiliki satu catatan absensi masuk untuk satu sesi, kecuali aturan koreksi atau pemindaian ulang disetujui.

### FR-06 — Absensi Keluar

- **Prioritas:** P0, dengan aturan yang perlu dikonfirmasi
- Sistem mendukung pencatatan waktu keluar.
- Waktu keluar hanya dapat dicatat untuk peserta yang memiliki catatan masuk pada sesi yang sama, kecuali kebijakan lain ditetapkan.
- Sistem mencegah pencatatan keluar berulang yang tidak sah.
- Data masuk dan keluar harus dapat dibedakan dan ditelusuri.

**[PERLU DIKONFIRMASI]** Apakah absensi keluar wajib bagi seluruh peserta, kapan periode keluar dibuka, dan apakah status hadir ditentukan dari absensi masuk saja atau dari pasangan masuk/keluar.

### FR-07 — Riwayat Absensi

- **Prioritas:** P0
- Admin dapat melihat riwayat berdasarkan peserta, tanggal, jadwal, dan status.
- Petugas hanya dapat melihat riwayat sesuai sesi atau tugasnya.
- Detail catatan minimal memuat peserta, jadwal, waktu masuk, waktu keluar jika ada, status, dan sumber pencatatan.
- Perubahan terhadap catatan yang sudah tersimpan harus dibatasi dan dicatat.

### FR-08 — Dashboard Statistik

- **Prioritas:** P0
- Dashboard menampilkan ringkasan yang relevan, seperti jumlah peserta aktif, jumlah jadwal, jumlah hadir, jumlah terlambat, dan jumlah belum tercatat.
- Statistik harus mengikuti filter tanggal/sesi yang dipilih.
- Definisi setiap metrik harus konsisten dan dijelaskan.
- Data yang belum tersedia harus ditampilkan sebagai kosong/nol secara tepat, bukan angka perkiraan.
- Statistik hanya boleh menampilkan data yang boleh diakses pengguna.

### FR-09 — Rekap dan Laporan

- **Prioritas:** P0
- Admin dapat membuat rekap berdasarkan tanggal, jadwal, peserta, program studi/unit (jika tersedia), dan status.
- Laporan membedakan hadir, terlambat, belum tercatat, serta status lain yang kelak disepakati.
- Laporan harus menggunakan sumber data yang sama dengan halaman riwayat.
- Filter yang digunakan harus terlihat pada laporan yang ditampilkan atau diekspor.

### FR-10 — Ekspor Excel dan PDF

- **Prioritas:** P1
- Admin dapat mengekspor laporan berdasarkan filter yang dipilih.
- Berkas harus memuat judul laporan, rentang tanggal/sesi, waktu pembuatan, dan kolom yang relevan.
- Isi ekspor harus sesuai dengan hasil filter dan hak akses.
- Nama file harus mudah dikenali.
- Sistem harus menangani kegagalan ekspor dengan pesan yang jelas.
- Data yang diekspor tidak boleh menyertakan rahasia sistem atau informasi yang tidak diperlukan.

**[PERLU DIKONFIRMASI]** Format Excel yang diinginkan (XLSX/CSV), format PDF, logo kampus, tanda tangan, dan format laporan resmi.

### FR-11 — Aplikasi Android

- **Prioritas:** P0 untuk operasional dasar; rincian integrasi perlu dikonfirmasi
- Aplikasi menyediakan login bagi petugas apel.
- Petugas dapat melihat jadwal dan status sesi.
- Petugas dapat melihat hasil pemindaian dan ringkasan sesi.
- Aplikasi harus memberikan status jaringan dan pesan kesalahan yang mudah dipahami.
- Aplikasi hanya menampilkan fitur sesuai hak akses petugas.
- Data sensitif tidak boleh disimpan di perangkat secara berlebihan.

**[PERLU DIKONFIRMASI]** Apakah aplikasi Android menerima hasil baca langsung dari reader melalui USB, Bluetooth, jaringan lokal, atau hanya menampilkan dashboard dan memanggil API backend.

### FR-12 — Integrasi RFID Reader dan Backend

- **Prioritas:** P0
- Sistem harus memiliki alur yang terdokumentasi dari pembacaan kartu sampai catatan absensi tersimpan.
- Backend harus memvalidasi kartu, peserta, sesi, waktu, dan duplikasi; jangan mempercayai keputusan status yang hanya dihitung oleh perangkat klien.
- Kegagalan jaringan atau backend harus menghasilkan status gagal yang jelas dan tidak boleh menampilkan keberhasilan palsu.
- Permintaan berulang akibat retry harus ditangani secara idempoten agar tidak membuat catatan ganda.
- Jika sistem harus mendukung absensi offline, mekanisme penyimpanan lokal, sinkronisasi, konflik, dan validasi waktunya harus dirancang sebagai kebutuhan tersendiri.

**[PERLU DIKONFIRMASI]** Reader, perangkat penghubung, protokol komunikasi, koneksi internet/jaringan lokal, format payload, autentikasi perangkat, dan kebutuhan offline.

### FR-13 — Audit Log

- **Prioritas:** P1
- Sistem mencatat aktivitas penting, seperti perubahan peserta, pemetaan kartu, perubahan jadwal, koreksi absensi, ekspor laporan, dan perubahan akun.
- Log minimal berisi pelaku, waktu, jenis tindakan, objek terkait, serta hasil tindakan.
- Log tidak menyimpan kata sandi, token, atau rahasia autentikasi.
- Pengguna biasa tidak dapat mengubah atau menghapus log audit.

### FR-14 — Pengelolaan Akun Petugas

- **Prioritas:** P0
- Admin dapat membuat, mengubah, menonaktifkan, dan mengatur akses akun petugas.
- Setiap petugas menggunakan akun masing-masing; akun bersama tidak disarankan.
- Penonaktifan akun harus mencegah akses berikutnya dan membatalkan sesi sesuai mekanisme autentikasi yang digunakan.

---

## 5. Aturan Bisnis

1. **Satu sumber waktu:** waktu absensi harus menggunakan waktu backend yang tersinkronisasi atau mekanisme waktu tepercaya yang disepakati.
2. **Jadwal aktif:** pemindaian hanya dapat diterima ketika sesi dan periode absensi sesuai aturan yang telah ditetapkan.
3. **Aturan terlambat:** admin menentukan batas waktu atau toleransi keterlambatan. Sistem menghitung status dari waktu masuk yang valid.
4. **Batas waktu harus eksplisit:** aturan mengenai apakah pemindaian tepat pada batas masih dianggap tepat waktu harus ditetapkan.
5. **Kartu terdaftar:** hanya kartu aktif yang terhubung ke peserta aktif yang dapat digunakan.
6. **Anti-duplikasi:** satu peserta tidak dapat membuat lebih dari satu absensi masuk untuk sesi yang sama melalui pemindaian berulang.
7. **Absensi keluar:** dicatat secara terpisah dan terhubung ke sesi serta catatan masuk yang sesuai.
8. **Perubahan data:** perubahan identitas peserta atau kartu tidak boleh mengubah riwayat kehadiran lama secara tidak sengaja.
9. **Koreksi manual:** jika diaktifkan, koreksi harus mencatat alasan, pelaku, waktu, nilai sebelum, dan nilai sesudah.
10. **Hak akses:** pengguna hanya dapat menjalankan fungsi dan melihat data sesuai perannya.
11. **Pembatalan jadwal:** pembatalan jadwal tidak boleh menghapus riwayat tanpa kebijakan retensi yang jelas.
12. **Ekspor:** hasil ekspor harus sesuai filter dan izin pengguna.
13. **Kegagalan operasi:** kegagalan membaca kartu, jaringan, atau penyimpanan harus dibedakan dari absensi berhasil.
14. **Waktu dan zona waktu:** zona waktu operasional harus dikonfigurasi secara konsisten di backend, database, web, dan Android.

### 5.1 Definisi Status Awal

Status berikut adalah usulan awal dan perlu disepakati:

- **Hadir:** absensi masuk valid dilakukan sebelum atau pada batas waktu tepat waktu.
- **Terlambat:** absensi masuk valid dilakukan setelah batas tepat waktu tetapi masih dalam periode absensi yang diterima.
- **Belum tercatat:** tidak ditemukan absensi masuk pada saat laporan dibuat.
- **Keluar tercatat:** waktu keluar telah tercatat; ini merupakan atribut tambahan, bukan pengganti status hadir/terlambat.
- **Ditolak:** hasil pemindaian yang tidak menghasilkan catatan absensi karena kartu, peserta, jadwal, atau aturan tidak valid.

**[PERLU DIKONFIRMASI]** Apakah perlu status izin, sakit, alpa, dispensasi, atau status lainnya. “Belum tercatat” tidak otomatis berarti alpa sebelum aturan resmi ditetapkan.

---

## 6. Alur Pengguna (User Flow)

### 6.1 Alur Admin

1. Admin membuka web dashboard.
2. Admin login.
3. Sistem memvalidasi akun dan peran.
4. Admin menambahkan atau mengimpor data peserta.
5. Admin mendaftarkan dan memetakan kartu RFID ke peserta.
6. Admin membuat jadwal apel.
7. Admin menentukan periode absensi dan batas keterlambatan.
8. Admin memastikan akun petugas dan perangkat operasional tersedia.
9. Saat apel berlangsung, sistem menerima catatan absensi dari alur RFID.
10. Admin melihat statistik, riwayat, dan laporan.
11. Admin memilih filter lalu mengekspor laporan bila diperlukan.
12. Admin logout.

### 6.2 Alur Petugas Apel

1. Petugas membuka aplikasi Android.
2. Petugas login.
3. Sistem memvalidasi akun dan peran.
4. Petugas memilih jadwal/sesi yang tersedia.
5. Aplikasi menampilkan status sesi dan kesiapan koneksi reader.
6. Peserta menempelkan kartu RFID ke reader.
7. Sistem memvalidasi kartu, peserta, jadwal, waktu, dan duplikasi.
8. Sistem menyimpan catatan jika valid dan menampilkan hasilnya.
9. Jika tidak valid, sistem menampilkan alasan yang dapat ditindaklanjuti tanpa membocorkan informasi sensitif.
10. Petugas memantau jumlah pemindaian dan ringkasan sesi.
11. Petugas menutup atau menyelesaikan sesi sesuai kewenangannya.
12. Petugas logout.

### 6.3 Alur Pendaftaran Kartu

1. Admin membuka detail peserta.
2. Admin memilih tindakan untuk mendaftarkan kartu.
3. Sistem masuk ke mode pembacaan kartu.
4. Kartu dibaca melalui mekanisme yang didukung perangkat.
5. Sistem memeriksa apakah identitas kartu sudah digunakan.
6. Jika belum digunakan, admin mengonfirmasi pemetaan.
7. Sistem menyimpan pemetaan dan menampilkan hasil.
8. Jika kartu sudah terhubung ke peserta lain, sistem menolak pemetaan dan memberi pesan yang sesuai.

### 6.4 Alur Gagal atau Tidak Valid

- Kartu tidak dikenal: tidak membuat catatan absensi.
- Peserta nonaktif: menolak pemindaian.
- Sesi tidak aktif atau di luar periode: menolak pemindaian sesuai aturan.
- Duplikat: menampilkan catatan yang sudah ada tanpa membuat duplikat.
- Jaringan/backend gagal: menampilkan kegagalan; tidak mengklaim absensi tersimpan.
- Reader tidak terhubung: menampilkan status perangkat dan instruksi pemeriksaan.
- Waktu perangkat berbeda: waktu perangkat tidak boleh mengesampingkan waktu backend tanpa mekanisme yang disepakati.

---

## 7. Kebutuhan Nonfungsional

### NFR-01 — Keamanan

- Password disimpan menggunakan hashing yang sesuai standar framework.
- Semua endpoint yang memerlukan login harus dilindungi autentikasi dan otorisasi di backend.
- Validasi input dilakukan di server, bukan hanya di browser/aplikasi.
- Gunakan HTTPS pada lingkungan produksi.
- Rahasia aplikasi dan kredensial database tidak boleh dimasukkan ke repository.
- Batasi percobaan login dan lindungi dari serangan umum sesuai kebutuhan risiko.
- Cegah SQL injection, XSS, CSRF pada konteks yang relevan, IDOR, mass assignment, dan eskalasi hak akses.
- Ekspor dan data riwayat wajib mematuhi kontrol akses.
- Gunakan prinsip hak akses minimum untuk admin, petugas, dan perangkat.
- Jika reader/perangkat terhubung ke API, autentikasi perangkat harus dirancang; jangan menganggap UID kartu sebagai bukti kriptografis yang kuat tanpa menilai tipe kartu.
- Catat aktivitas penting tanpa merekam rahasia.

### NFR-02 — Kinerja

- Halaman umum harus memberikan umpan balik pemuatan yang jelas.
- Hasil pemindaian harus diproses sesegera mungkin sesuai kemampuan reader, jaringan, dan server.
- Query riwayat dan laporan harus menggunakan pagination/filter dan indeks yang sesuai.
- **Target waktu respons [PERLU DIKONFIRMASI]:** tetapkan setelah jumlah peserta, volume absensi, dan spesifikasi server diketahui.

### NFR-03 — Keandalan dan Integritas Data

- Catatan absensi tidak boleh hilang diam-diam ketika terjadi kesalahan.
- Proses penyimpanan harus menggunakan transaksi database bila diperlukan.
- Permintaan berulang tidak boleh menciptakan catatan ganda.
- Backup database dan prosedur pemulihan harus ditetapkan sebelum produksi.
- Kegagalan harus dicatat dan dapat ditelusuri tanpa menyimpan data rahasia.

### NFR-04 — Kegunaan dan Aksesibilitas

- Antarmuka web dan Android harus menggunakan istilah yang konsisten.
- Status berhasil, terlambat, ditolak, dan gagal harus dapat dibedakan secara visual dan melalui teks.
- Jangan mengandalkan warna saja untuk menyampaikan status.
- Formulir harus memvalidasi input dan menampilkan pesan kesalahan yang mudah dipahami.
- Alur pemindaian harus meminimalkan tindakan manual petugas.

### NFR-05 — Kompatibilitas

- Web harus mendukung browser modern yang ditetapkan untuk operasional kampus.
- Versi Android minimum dan daftar perangkat yang didukung **[PERLU DIKONFIRMASI]**.
- Kompatibilitas reader, driver, SDK, USB/Bluetooth/network, dan metode komunikasi harus dibuktikan melalui prototipe perangkat sebelum implementasi final.

### NFR-06 — Privasi dan Retensi

- Kumpulkan data peserta seminimal mungkin untuk kebutuhan absensi.
- Batasi akses ke data pribadi berdasarkan peran.
- Kebijakan retensi, pengarsipan, ekspor, dan penghapusan data harus disepakati dengan kampus.
- Data produksi tidak boleh digunakan dalam pengujian tanpa perlindungan atau anonimisasi yang sesuai.

---

## 8. Kebutuhan Data dan Entitas Awal

Struktur berikut adalah rancangan konseptual, bukan skema database final.

### 8.1 Entitas

1. **users**
   - Identitas akun, nama, email/username, password hash, peran, status aktif, timestamps.
2. **participants**
   - Nomor induk, nama, atribut unit akademik jika dibutuhkan, status aktif, timestamps.
3. **rfid_cards**
   - Identitas kartu/UID sesuai reader, participant_id, status aktif, waktu pendaftaran dan penonaktifan.
4. **apel_schedules**
   - Judul, tanggal, waktu mulai/akhir, periode absensi, batas terlambat, status, zona waktu.
5. **attendance_records**
   - Peserta, jadwal, waktu masuk, waktu keluar, status, sumber pencatatan, timestamps.
6. **attendance_events** (opsional, disarankan jika perlu jejak pemindaian)
   - Identitas kartu atau referensi kartu, jadwal, waktu kejadian, hasil validasi, alasan penolakan, referensi catatan jika ada.
7. **audit_logs**
   - Pelaku, aksi, objek, waktu, metadata yang aman.
8. **device_registry** (opsional, tergantung arsitektur)
   - Identitas perangkat, status, metode autentikasi/credential reference, waktu terakhir aktif.

### 8.2 Integritas dan Relasi

- Nomor induk peserta harus unik sesuai aturan yang disepakati.
- Identitas kartu aktif harus unik.
- Catatan absensi harus mencegah duplikasi berdasarkan kombinasi peserta dan jadwal untuk absensi masuk.
- Catatan absensi terhubung ke peserta dan jadwal yang valid.
- Perubahan kartu tidak boleh merusak keterlacakan riwayat lama.
- Foreign key dan indeks perlu dirancang setelah skema final disetujui.

### 8.3 Retensi Data

**[PERLU DIKONFIRMASI]** Lama penyimpanan catatan absensi, log penolakan, audit log, dan backup; siapa yang berwenang menghapus atau mengarsipkan data.

---

## 9. Kebutuhan Antarmuka

### 9.1 Web Dashboard Admin

Halaman minimum yang disarankan:

- Login.
- Dashboard statistik.
- Data peserta.
- Detail peserta dan pemetaan kartu.
- Manajemen jadwal apel.
- Manajemen akun petugas.
- Riwayat absensi.
- Rekap dan laporan.
- Ekspor Excel/PDF.
- Audit log jika disetujui.

### 9.2 Aplikasi Android Petugas

Halaman minimum yang disarankan:

- Login.
- Daftar jadwal/sesi.
- Detail sesi dan status kesiapan.
- Tampilan pemindaian/hasil pemindaian.
- Ringkasan jumlah hadir dan terlambat.
- Riwayat sesi sesuai hak akses.
- Status koneksi dan pesan kesalahan.

### 9.3 Prinsip UX

- Antarmuka harus fokus pada tugas operasional dan mudah digunakan dalam kondisi apel berlangsung.
- Informasi hasil pemindaian harus mudah dibaca dengan cepat.
- Gunakan tampilan loading, kosong, gagal, dan berhasil.
- Hindari tombol penting tanpa konfirmasi untuk tindakan yang berdampak pada data.
- Desain visual akhir, branding kampus, dan tema warna **[PERLU DIKONFIRMASI]**.

---

## 10. API dan Integrasi

Backend Laravel menjadi sumber utama validasi aturan absensi dan penyimpanan data. Bentuk API final mengikuti pilihan arsitektur autentikasi dan komunikasi perangkat.

Kelompok endpoint konseptual:

- Autentikasi pengguna.
- CRUD peserta.
- Pemetaan dan penonaktifan kartu.
- CRUD jadwal apel.
- Memulai/mengakhiri sesi jika diperlukan.
- Pengiriman hasil baca RFID/permintaan absensi.
- Absensi masuk dan keluar.
- Riwayat dan rekap.
- Statistik dashboard.
- Ekspor laporan.
- Manajemen pengguna petugas.
- Audit log.

**Belum ditetapkan:** URL endpoint, format payload, token autentikasi, API versioning, mekanisme autentikasi perangkat, metode koneksi reader, retry, dan dukungan offline. Semua ini harus ditentukan setelah hardware dan alur komunikasi dipilih.

---

## 11. Acceptance Criteria (Kriteria Penerimaan)

Produk versi awal dapat diterima apabila skenario berikut lulus pengujian.

### AC-01 — Login dan Hak Akses

- Admin dapat login dengan kredensial valid.
- Petugas dapat login dengan kredensial valid.
- Kredensial tidak valid ditolak dengan pesan aman.
- Petugas tidak dapat mengakses endpoint admin hanya dengan memanggil API secara langsung.
- Akun nonaktif tidak dapat melakukan operasi yang dilindungi.

### AC-02 — Data Peserta dan Kartu

- Admin dapat membuat dan memperbarui peserta.
- Sistem menolak nomor induk duplikat sesuai aturan.
- Admin dapat memetakan kartu ke peserta.
- Sistem menolak pemetaan kartu aktif yang sudah digunakan peserta lain.
- Kartu atau peserta nonaktif tidak dapat membuat absensi baru.

### AC-03 — Jadwal dan Keterlambatan

- Admin dapat membuat jadwal dengan periode absensi dan batas keterlambatan.
- Sistem menolak konfigurasi waktu yang tidak valid.
- Pemindaian pada waktu tepat waktu menghasilkan status sesuai definisi batas yang telah disetujui.
- Pemindaian setelah batas tepat waktu tetapi dalam periode yang diperbolehkan menghasilkan status terlambat.
- Pemindaian di luar periode absensi ditolak sesuai aturan.
- Uji batas waktu mencakup waktu tepat sebelum, tepat pada, dan tepat setelah batas.

### AC-04 — Absensi Masuk dan Duplikasi

- Kartu valid untuk peserta aktif pada sesi aktif menghasilkan satu catatan masuk.
- Pemindaian kartu yang sama berulang kali tidak membuat catatan masuk ganda.
- Kartu tidak dikenal, sesi tidak aktif, dan peserta nonaktif ditolak.
- Jika penyimpanan gagal, sistem tidak menampilkan status berhasil.
- Retry permintaan yang sama tidak menggandakan catatan.

### AC-05 — Absensi Keluar

- Absensi keluar yang valid tersimpan pada catatan peserta dan sesi yang benar.
- Absensi keluar tanpa catatan masuk ditangani sesuai aturan yang disetujui.
- Pemindaian keluar berulang tidak mengubah catatan secara tidak sah.

### AC-06 — Riwayat, Statistik, dan Laporan

- Admin dapat menemukan riwayat dengan filter yang disediakan.
- Jumlah statistik cocok dengan data sumber pada filter yang sama.
- Petugas tidak dapat melihat data di luar kewenangannya.
- Ekspor Excel/PDF sesuai filter, memuat kolom yang disepakati, dan dapat dibuka.
- Data yang dibatalkan atau dikoreksi ditampilkan sesuai kebijakan yang ditetapkan.

### AC-07 — Integrasi Perangkat

- Pembacaan kartu melalui reader terpilih menghasilkan identitas yang dapat diproses sistem.
- Koneksi putus ditampilkan sebagai kegagalan, bukan keberhasilan.
- Perangkat yang tidak terdaftar atau tidak terautentikasi ditolak jika registrasi perangkat menjadi bagian arsitektur.
- Pengujian dilakukan pada model reader dan perangkat Android yang benar-benar dipilih.

### AC-08 — Keamanan dan Audit

- Endpoint terlindungi oleh autentikasi dan otorisasi server.
- Input tidak valid ditolak dengan aman.
- Aktivitas sensitif yang diwajibkan tercatat di audit log.
- Log tidak mengandung kata sandi atau token.
- Backup dan pemulihan diuji sebelum sistem digunakan untuk data produksi.

---

## 12. Pengujian

Jenis pengujian yang disarankan:

- **Unit test:** aturan keterlambatan, validasi jadwal, pemetaan kartu, dan aturan duplikasi.
- **Feature/API test:** autentikasi, otorisasi, CRUD, absensi, riwayat, dan ekspor.
- **Integration test:** komunikasi reader, backend Laravel, database, dan aplikasi Android.
- **Security test:** akses lintas peran, manipulasi ID, validasi input, session/token, dan akses ekspor.
- **User acceptance test (UAT):** admin dan petugas menjalankan skenario apel simulasi.
- **Device test:** uji kartu valid/tidak dikenal, pembacaan berulang, jaringan putus, dan kondisi perangkat sebenarnya.
- **Data reconciliation:** bandingkan jumlah pemindaian yang diterima, catatan tersimpan, dan laporan akhir.

---

## 13. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Reader belum dipilih | Integrasi bisa berubah atau tidak kompatibel | Konfirmasi model dan lakukan proof of concept sebelum implementasi final |
| UID kartu dapat disalin pada tipe kartu tertentu | Risiko pemalsuan kehadiran | Evaluasi tipe kartu; jangan menganggap UID sebagai autentikasi kriptografis |
| Jaringan terputus saat apel | Absensi gagal atau tertunda | Tentukan apakah mode offline diperlukan dan rancang sinkronisasi secara eksplisit |
| Pemindaian berulang | Catatan ganda | Terapkan constraint database dan operasi idempoten |
| Jam perangkat tidak akurat | Status keterlambatan salah | Gunakan waktu backend atau sumber waktu tepercaya |
| Salah konfigurasi jadwal | Banyak catatan ditolak atau status salah | Validasi konfigurasi dan uji batas waktu |
| Hak akses terlalu luas | Kebocoran atau manipulasi data | Terapkan role/permission dan pengujian akses server |
| Ekspor data tidak terkontrol | Data peserta tersebar | Batasi izin ekspor dan catat aktivitasnya |
| Backup tidak tersedia | Kehilangan data | Jadwalkan backup dan uji pemulihan |

---

## 14. Asumsi dan Hal yang Belum Diputuskan

Daftar ini wajib ditinjau sebelum pengembangan final.

### Hardware dan koneksi
- **[PERLU DIKONFIRMASI]** Merek/model reader dan tipe kartu/tag RFID.
- **[PERLU DIKONFIRMASI]** Apakah reader terhubung ke Android, komputer, mikrokontroler, atau jaringan.
- **[PERLU DIKONFIRMASI]** Metode komunikasi: USB, Bluetooth, Wi-Fi/LAN, serial, atau SDK/vendor.
- **[PERLU DIKONFIRMASI]** Apakah reader mengirim UID saja atau data lain.
- **[PERLU DIKONFIRMASI]** Apakah koneksi internet harus selalu aktif atau absensi offline diperlukan.
- **[PERLU DIKONFIRMASI]** Apakah satu reader melayani satu sesi atau beberapa titik absensi.

### Kebijakan absensi
- **[PERLU DIKONFIRMASI]** Apakah absensi keluar wajib.
- **[PERLU DIKONFIRMASI]** Batas waktu masuk, toleransi keterlambatan, dan periode penutupan.
- **[PERLU DIKONFIRMASI]** Apakah peserta boleh melakukan absensi di beberapa titik.
- **[PERLU DIKONFIRMASI]** Kebijakan izin, sakit, dispensasi, koreksi manual, dan peserta tanpa kartu.
- **[PERLU DIKONFIRMASI]** Jadwal berulang, lokasi apel, unit peserta, dan penugasan petugas.

### Data dan pengguna
- **[PERLU DIKONFIRMASI]** Format nomor induk dan kolom peserta wajib.
- **[PERLU DIKONFIRMASI]** Cara impor data peserta dan penerbitan kartu.
- **[PERLU DIKONFIRMASI]** Apakah perlu akun pimpinan atau portal peserta.
- **[PERLU DIKONFIRMASI]** Jumlah peserta, petugas, jadwal, dan volume pemindaian.

### Infrastruktur dan operasional
- **[PERLU DIKONFIRMASI]** Versi Laravel/PHP, lingkungan hosting, dan database MySQL yang akan digunakan.
- **[PERLU DIKONFIRMASI]** Versi minimum Android dan daftar perangkat yang akan dipakai.
- **[PERLU DIKONFIRMASI]** Format ekspor resmi, branding, retensi data, backup, dan target performa.
- **[PERLU DIKONFIRMASI]** Apakah perlu integrasi SSO atau sistem akademik kampus.

---

## 15. Rencana Implementasi Bertahap

### Tahap 1 — Konfirmasi dan Proof of Concept
- Pilih reader dan kartu.
- Buktikan pembacaan kartu pada perangkat target.
- Tetapkan metode komunikasi reader–Android/backend.
- Sepakati aturan absensi dan batas keterlambatan.
- Konfirmasi kolom data peserta dan hak akses.

**Kriteria keluar:** kartu dapat dibaca secara konsisten dan jalur komunikasi teknis telah dipilih.

### Tahap 2 — Backend dan Dashboard Inti
- Autentikasi dan hak akses.
- Data peserta dan pemetaan kartu.
- Jadwal apel dan aturan waktu.
- API absensi dan pencegahan duplikasi.
- Riwayat absensi.

**Kriteria keluar:** skenario inti lulus unit test dan API test.

### Tahap 3 — Integrasi Android dan RFID
- Login petugas.
- Tampilan jadwal dan sesi.
- Integrasi reader sesuai protokol yang dipilih.
- Umpan balik pemindaian dan penanganan kegagalan.
- Uji perangkat nyata.

**Kriteria keluar:** alur baca kartu sampai penyimpanan backend bekerja pada perangkat target.

### Tahap 4 — Statistik dan Laporan
- Dashboard statistik.
- Filter riwayat dan rekap.
- Ekspor Excel/PDF.
- Verifikasi konsistensi angka laporan.

**Kriteria keluar:** hasil laporan cocok dengan catatan sumber.

### Tahap 5 — Keamanan, UAT, dan Rilis
- Uji hak akses dan skenario keamanan.
- Backup serta uji pemulihan.
- UAT bersama admin dan petugas.
- Dokumentasi instalasi dan panduan operasional.
- Perbaikan temuan sebelum produksi.

**Kriteria keluar:** seluruh acceptance criteria prioritas P0 lulus dan pemilik sistem menyetujui hasil UAT.

---

## 16. Definisi Selesai (Definition of Done)

Sebuah fitur dinyatakan selesai jika:

1. Implementasi memenuhi requirement terkait.
2. Validasi dilakukan di backend untuk operasi yang sensitif.
3. Hak akses diuji untuk peran yang relevan.
4. Loading, kosong, berhasil, dan gagal ditangani dengan benar.
5. Unit test/API test yang relevan lulus.
6. Tidak ada catatan duplikat akibat retry atau pemindaian berulang.
7. Dokumentasi penggunaan atau konfigurasi diperbarui bila diperlukan.
8. Tidak ada bug kritis terbuka yang memengaruhi integritas absensi.
9. Perubahan telah ditinjau dan disetujui sesuai proses pengembangan proyek.

---

## 17. Ringkasan Keputusan

### Sudah disepakati
- Produk: sistem absensi apel berbasis RFID untuk kampus/perguruan tinggi.
- Platform: web dashboard dan aplikasi Android dengan perangkat RFID.
- Peran utama: admin dan petugas apel.
- Backend: Laravel.
- Database: MySQL.
- Aturan: admin mengatur waktu absensi; sistem mencatat keterlambatan otomatis.
- Fitur: data peserta, absensi masuk/keluar, rekap/laporan, riwayat peserta, dashboard statistik, ekspor Excel/PDF, dan manajemen jadwal apel.

### Belum disepakati
- Model reader, tipe kartu, konektivitas, protokol, dan arsitektur integrasi.
- Dukungan offline.
- Detail kebijakan masuk/keluar dan toleransi keterlambatan.
- Kolom peserta dan format impor.
- Versi framework, infrastruktur, target performa, retensi, dan format laporan resmi.

**Prinsip utama pengembangan:** jangan menetapkan detail yang belum dikonfirmasi sebagai fakta. Validasi perangkat dan aturan absensi terlebih dahulu, kemudian finalisasi desain API, database, dan implementasi.
