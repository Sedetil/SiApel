# Design System & Style Guide: SIABSEN POLINELA

> **Sistem Informasi Absensi Berbasis Lokasi**  
> **Versi:** 1.0 / Tema Terang  
> **Dokumen:** 01 / 03 — Fondasi (Warna · Tipografi · Geometri)

---

## 1. Fondasi Visual (Core Foundation)

> *"Token visual untuk membangun sistem informasi absensi berbasis lokasi yang tenang, jelas, dan terasa satu kesatuan."*

Style guide ini menjadi acuan tunggal (*single source of truth*) dalam pengembangan antarmuka (UI) untuk **SIABSEN POLINELA**. Seluruh komponen visual harus mengikuti token warna, hierarki tipografi, ritme spacing 4px, dan context-based border radius yang telah distandarkan.

---

## 2. Palet Warna (Color Palette)

> **Prinsip Utama:** Biru sebagai aksen utama. Warna semantik hanya digunakan untuk menyampaikan status.

### A. Merek & Aksen (Brand & Blue)

| Token Name | Hex Code | Visual Swatch | Fungsi & Penggunaan |
| :--- | :---: | :---: | :--- |
| **Primary (Utama)** | `#2D56FF` | `■` `#2D56FF` | Tombol CTA utama, status aktif, navigasi prioritas tinggi |
| **Accent** | `#4D7BFF` | `■` `#4D7BFF` | Hover state, highlight, badge aktif, aksen grafis |
| **Primary Dark** | `#1B37B9` | `■` `#1B37B9` | State active/pressed tombol, header kontras, border aktif |
| **Background** | `#F4F6FC` | `■` `#F4F6FC` | Kanvas latar belakang aplikasi (Light canvas) |
| **Surface** | `#FFFFFF` | `■` `#FFFFFF` | Latar kartu (card), kontainer, dropdown, modal |
| **Border** | `#D9D9D9` | `■` `#D9D9D9` | Garis pemisah (*divider*), border kartu, border kontrol input |
| **Text Primary** | `#0F1729` | `■` `#0F1729` | Teks judul, label utama, konten body penting |
| **Text Secondary** | `#6B7A90` | `■` `#6B7A90` | Teks sekunder, deskripsi pendukung, metadata, placeholder |

---

### B. Netral, Permukaan & Teks (Neutral, Surface & Typography)

| Token Name | Hex Code | Deskripsi & Konteks |
| :--- | :---: | :--- |
| **Surface** | `#FFFFFF` | **Permukaan:** Dipakai untuk latar elemen kontainer yang mengambang di atas background |
| **Background** | `#F4F6FC` | **Kanvas:** Warna dasar seluruh halaman web/dashboard |
| **Border** | `#D9D9D9` | **Garis:** Border dengan kontras lembut untuk membatasi komponen |
| **Text Secondary** | `#6B7A90` | **Sekunder:** Warna teks sekunder untuk hierarki informasi lapis kedua |
| **Text Primary** | `#0F1729` | **Teks Utama:** Kontras tajam dan nyaman dibaca untuk semua informasi vital |

---

### C. Semantik & Status (Semantic / Status Colors)

| Status | Hex Code | Visual Swatch | Fungsi & Konteks Penggunaan |
| :--- | :---: | :---: | :--- |
| **Success** | `#22C55E` | `■` `#22C55E` | **Konfirmasi:** Status hadir, berhasil absensi, dalam radius apel, validasi sukses |
| **Warning** | `#F5A623` | `■` `#F5A623` | **Perhatian:** Terlambat, izin/sakit butuh verifikasi, mendekati batas radius absensi |
| **Error** | `#FF0004` | `■` `#FF0004` | **Validasi:** Alpa/tidak hadir, di luar radius lokasi (out-of-bound), verifikasi gagal |

---

### D. Aturan Kontras & Teks

- **Teks Utama:** Seluruh teks heading dan body memakai warna `Text Primary (#0F1729)`.
- **Tombol Aksi Utama:** Selalu gunakan teks putih (`#FFFFFF`) di atas background `Primary (#2D56FF)`.
- **Warna Semantik:** Jangan jadikan warna semantik sebagai warna dekoratif halaman. Gunakan semata-mata untuk mengindikasikan status kehadiran, feedback proses, atau peringatan.

---

## 3. Tipografi (Typography)

> **Prinsip Utama:** *"Hierarki jelas, nyaman dipindai."*

### Font Families
1. **DM Sans** — Font utama untuk Heading (H1, H2, H3), Body text, Label, dan Caption.
2. **IBM Plex Mono** — Font khusus angka metrik, persentase statistik, kode kehadiran, dan data numerik.
3. **Inter** — Font alternatif / fallback modern untuk elemen interface yang rapat.

### Bobot Huruf (Font Weights)
- **Regular:** `400`
- **Medium:** `500`
- **Semibold:** `600`
- **Bold:** `700`

---

### Skala & Hierarki Tipografi

| Nama Level | Ukuran | Bobot / Font Family | Contoh Teks | Penggunaan Spesifik |
| :--- | :---: | :--- | :--- | :--- |
| **Stat** | `32px` | **Bold** (`IBM Plex Mono`) | `98%` | Angka KPI, persentase kehadiran apel, metrik ringkasan |
| **H1** | `24px` | **Bold** (`DM Sans`) | `Absensi Kompi A` | Judul utama halaman / header modul |
| **H2** | `20px` | **Bold** (`DM Sans`) | `Ringkasan kehadiran` | Judul kartu besar / seksi utama |
| **H3** | `16px` | **Bold** (`DM Sans`) | `Per kompi` | Sub-heading dalam kartu / header tabel |
| **Body besar** | `16px` | **Regular** (`DM Sans`) | `Pantau kehadiran mahasiswa secara real-time per kompi.` | Lead paragraph, intro deskriptif halaman |
| **Body** | `14px` | **Regular** (`DM Sans`) | `Lihat detail absensi, lokasi, dan status kehadiran mahasiswa.` | Teks isi standar, isi tabel data, keterangan form |
| **Label** | `12px` | **Bold** (`DM Sans`) | `Kompi A - 24 mahasiswa` | Label input, badge keterangan, pill info, tag metadata |
| **Caption** | `10px` | **Bold** (`DM Sans`) | `Terakhir diperbarui 5 menit lalu` | Timestamp, status kecil, catatan kaki, legal/copyright |

---

## 4. Spacing & Radius (Geometri)

> **Prinsip Utama:** *"Ritme 4 px, ruang yang terukur, sudut yang ramah."*

### A. Skala Spacing (Kelipatan 4)

| Token | Nilai | Visualisasi Grid | Penggunaan |
| :--- | :---: | :--- | :--- |
| `space-4` | `4px` | ▇ | Jarak mikro antar icon dan teks ringkas |
| `space-8` | `8px` | ▇▇ | Jarak antar elemen terkait (input label ke input field) |
| `space-12` | `12px` | ▇▇▇ | Gap antar item dalam baris/badge |
| `space-16` | `16px` | ▇▇▇▇ | Padding internal tombol, input form (*control padding*) |
| `space-24` | `24px` | ▇▇▇▇▇▇ | Padding internal kartu (card padding), gap kolom grid |
| `space-32` | `32px` | ▇▇▇▇▇▇▇▇ | Jarak antar kelompok/section dalam 1 halaman |
| `space-48` | `48px` | ▇▇▇▇▇▇▇▇▇▇▇▇ | Margin section besar, hero header |
| `space-64` | `64px` | ▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇▇ | Separasi modul utama halaman |

---

### B. Radius (Sesuai Konteks)

| Radius | Konteks | Komponen Target |
| :---: | :--- | :--- |
| **6 px** | **Kecil** | Badge, Tag status, Chip, Tooltip |
| **8 px** | **Kontrol** | Tombol (Button), Form Input, Select dropdown, Checkbox/Radio wrapper |
| **12 px** | **Kartu** | Card container, Dialog modal, Floating popover, Widget |
| **16 px** | **Panel** | Sidebar navigation sheet, Drawer, Main content container panel |

---

### C. Aturan "Ruang yang Bernapas"

- **`8 px`** antar elemen terkait *(misal: icon dengan labelnya, atau label form dengan field input)*.
- **`16 px`** di dalam kontrol *(padding horizontal input dan button)*.
- **`24 px`** di dalam kartu *(padding internal card/box container)*.
- **`32 px`** antar kelompok *(margin bawah atau gap antar grup kartu/section)*.

---

## 5. Implementasi Teknis (CSS & Tailwind CSS v4)

Gunakan variabel CSS dan tema Tailwind berikut untuk memastikan kepatuhan desain:

```css
/* resources/css/app.css */
@import 'tailwindcss';

@theme {
  /* Font Families */
  --font-sans: 'DM Sans', 'Inter', system-ui, -apple-system, sans-serif;
  --font-mono: 'IBM Plex Mono', monospace;

  /* Colors - Brand & Primary */
  --color-primary: #2D56FF;
  --color-primary-dark: #1B37B9;
  --color-accent: #4D7BFF;

  /* Colors - Surface & Neutral */
  --color-background: #F4F6FC;
  --color-surface: #FFFFFF;
  --color-border: #D9D9D9;
  --color-text-primary: #0F1729;
  --color-text-secondary: #6B7A90;

  /* Colors - Semantic Status */
  --color-success: #22C55E;
  --color-warning: #F5A623;
  --color-error: #FF0004;

  /* Border Radius Context */
  --radius-sm: 6px;    /* Kecil: Badge/Tag */
  --radius-md: 8px;    /* Kontrol: Button/Input */
  --radius-lg: 12px;   /* Kartu: Card/Modal */
  --radius-xl: 16px;   /* Panel: Sheet/Drawer */
}
```

### Google Fonts Import
Tambahkan Google Fonts pada layout Blade utama (`resources/views/layouts/app.blade.php` atau sejenis):

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=IBM+Plex+Mono:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

---

## 6. Contoh Penggunaan Komponen (Component Snippets)

### Tombol Utama (Primary Action)
```html
<button class="bg-[#2D56FF] hover:bg-[#1B37B9] text-white text-[14px] font-medium px-4 py-2 rounded-[8px] transition-colors duration-150">
    Konfirmasi Kehadiran
</button>
```

### Kartu Metrik Kehadiran (Stat Card)
```html
<div class="bg-white border border-[#D9D9D9] rounded-[12px] p-6 shadow-sm">
    <div class="flex items-center justify-between mb-2">
        <span class="text-[#6B7A90] text-[12px] font-bold uppercase tracking-wider">Tingkat Kehadiran</span>
        <span class="inline-flex items-center px-2 py-0.5 rounded-[6px] text-[10px] font-bold bg-[#22C55E]/10 text-[#22C55E]">
            Valid
        </span>
    </div>
    <div class="text-[#0F1729] font-mono text-[32px] font-bold">
        98%
    </div>
    <p class="text-[#6B7A90] text-[12px] mt-2">
        Terakhir diperbarui 5 menit lalu
    </p>
</div>
```

### Status Badge (Semantic Pills)
```html
<!-- Hadir / Sukses -->
<span class="inline-flex items-center px-2.5 py-1 rounded-[6px] text-[12px] font-bold bg-[#22C55E]/10 text-[#22C55E]">
    Hadir Tepat Waktu
</span>

<!-- Terlambat / Peringatan -->
<span class="inline-flex items-center px-2.5 py-1 rounded-[6px] text-[12px] font-bold bg-[#F5A623]/10 text-[#F5A623]">
    Terlambat Apel
</span>

<!-- Alpa / Error -->
<span class="inline-flex items-center px-2.5 py-1 rounded-[6px] text-[12px] font-bold bg-[#FF0004]/10 text-[#FF0004]">
    Di Luar Radius
</span>
```
