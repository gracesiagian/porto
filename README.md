# Dimas Arya — Graphic Design Portfolio & CMS Admin Panel

Sebuah website portofolio desain grafis modern, estetis, dan responsif dengan panel administrasi konten (CMS) terintegrasi untuk menggantikan website Carrd legacy.

---

## ✨ Fitur Utama

### 1. Landing Page (Aesthetic Carrd Floating Card)
* **Floating Profile Card**: Kartu profil minimalis di tengah dengan soft drop shadow, border halus, dan rounded corners.
* **Status Badge & Avatar**: Menampilkan indikator ketersediaan real-time (*Open for Commissions / Freelance*).
* **Bio & Tagline**: Ringkasan profesional dan pengantar keahlian visual.
* **Action Pill Buttons**:
  * **Lihat Portofolio**: Smooth scroll / navigasi instan ke galeri multi-kolom.
  * **Twitter / X**: Tautan langsung ke profil X/Twitter.
  * **WhatsApp**: Direct chat link dengan pesan konsultasi otomatis.
  * **Discreet Admin Lock**: Akses aman ke panel CMS.

### 2. Galeri Portofolio Multi-Kolom & Lightbox (Solusi Keterbatasan Carrd)
* **Fluid Responsive Grid**: Tampilan galeri luas (1 kolom di mobile, 2–3 kolom di tablet, 3–4 kolom di desktop) tanpa batasan modal sempit Carrd.
* **Filter Kategori Dinamis**: Tab filter instan (*All, Thumbnail, Poster / Infografis, Pricelist, Laporan Diklat, Social Media*).
* **Interactive Lightbox Modal**:
  * Preview karya resolusi tinggi dengan zoom.
  * Detail judul, kategori, catatan klien, dan tools desain (*Photoshop, Illustrator, Figma*).
  * Tombol navigasi Previous/Next dan shortcut keyboard (`Esc`, `ArrowLeft`, `ArrowRight`).
  * Tombol langsung *"Pesan Desain Serupa"* via WhatsApp.

### 3. Integrated Admin CMS Panel (`/admin`)
* **Autentikasi Aman**: Login berbasis session PHP dengan proteksi `password_hash()` dan CSRF token.
* **Manajemen Karya (CRUD)**:
  * Upload karya desain baru (.png, .jpg, .webp, .svg) dengan live preview.
  * Update judul, ganti gambar, ubah kategori, dan atur urutan tampil (*display order*).
  * Toggle visibilitas (*Tampil / Draft*) dalam satu klik.
  * Hapus karya dengan modal konfirmasi.
* **Manajemen Kategori**: Tambah, edit, dan hapus kategori portofolio secara dinamis.
* **Pengaturan Profil & Sosial**: Ubah nomor WhatsApp, pesan default, URL Twitter/X, Instagram, bio, dan foto avatar langsung dari dashboard tanpa menyentuh kode.
* **Ganti Password**: Perbarui kata sandi admin secara aman.

---

## 🛠️ Tech Stack
* **Backend:** PHP 8.2 (Modular Architecture dengan PDO Prepared Statements)
* **Database:** MySQL (`porto_db`)
* **Styling:** Tailwind CSS + Custom Aesthetic Design System
* **Typography:** Google Fonts (*Plus Jakarta Sans*)
* **Icons:** Lucide Icons
* **Frontend Logic:** Vanilla JavaScript (ES6+)

---

## 🚀 Panduan Instalasi & Penggunaan (XAMPP)

### 1. Database Setup
1. Buka **XAMPP Control Panel** dan pastikan **Apache** serta **MySQL** dalam status `Running`.
2. Buka browser ke `http://localhost/phpmyadmin/` atau jalankan via CLI:
   ```bash
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS porto_db;"
   mysql -u root porto_db < database.sql
   ```
3. Seluruh tabel (`users`, `site_settings`, `categories`, `portfolio_items`) beserta 12 sample karya desain dan 5 kategori akan otomatis terisi.

### 2. Akun Login Admin Default
* **URL Login:** `http://localhost/porto/admin/login.php` (atau `http://localhost:8000/admin/login.php`)
* **Username:** `admin`
* **Password:** `admin123`

*(Password dapat diubah kapan saja melalui menu **Profil & Kontak** di Admin Panel).*

---

## 📁 Struktur Direktori
```
porto/
├── config/
│   ├── database.php          # Koneksi PDO singleton MySQL
│   └── helpers.php           # Helper keamanan, CSRF, upload handler, session
├── includes/
│   ├── header.php            # HTML Head, Tailwind CDN, Google Fonts, Lucide
│   ├── footer.php            # Footer branding, Back to top, scripts
│   └── auth.php              # Helper autentikasi & proteksi sesi
├── assets/
│   ├── css/custom.css        # Styling micro-interactions, lightbox, glassmorphism
│   ├── js/main.js            # Filter tabs, Lightbox modal, smooth scroll
│   ├── js/admin.js           # Admin image preview, live search, dynamic modals
│   └── images/               # Seed SVG graphics & default avatar
├── uploads/
│   ├── portfolio/            # Folder upload artwork karya desain
│   └── avatar/               # Folder upload foto avatar profil
├── admin/
│   ├── index.php             # Dashboard ringkasan & statistik
│   ├── login.php             # Halaman login estetis
│   ├── logout.php            # Handler logout
│   ├── portfolio.php         # Manajemen karya (CRUD Table & Modals)
│   ├── portfolio_action.php  # Backend processor upload & aksi karya
│   ├── categories.php        # Manajemen kategori desain
│   ├── categories_action.php # Backend processor kategori
│   ├── settings.php          # Pengaturan profil, WhatsApp & password
│   ├── settings_action.php   # Backend processor pengaturan
│   └── includes/             # Header & Footer panel admin
├── index.php                 # Halaman publik: Floating Card + Fluid Gallery + Lightbox
└── database.sql              # Skrip skema database & seed data
```
