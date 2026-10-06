# Sistem Informasi SOP Pelayanan Disdukcapil

Aplikasi web untuk memublikasikan dan mengelola informasi Standar Operasional Prosedur (SOP) pelayanan administrasi kependudukan. Proyek ini dikembangkan oleh **Micko Adrian** sebagai bagian dari kegiatan kerja praktik pada Bidang Pengelolaan Informasi Administrasi Kependudukan, Disdukcapil Kota Langsa.

> Proyek ini merupakan karya pembelajaran dan portofolio, bukan situs resmi atau sistem produksi Disdukcapil Kota Langsa.

## Fitur

- Daftar SOP pelayanan yang dapat diakses masyarakat.
- Halaman detail SOP beserta dokumen PDF dan gambar pendukung.
- Autentikasi administrator menggunakan kata sandi terenkripsi.
- Pengelolaan data SOP: tambah, lihat, ubah, dan hapus.
- Validasi tipe serta ukuran berkas yang diunggah.
- Perlindungan CSRF untuk operasi administrator.
- Tampilan responsif untuk desktop dan perangkat seluler.

## Teknologi

- PHP
- MySQL/MariaDB
- HTML dan CSS
- JavaScript

Setiap perubahan kode diperiksa otomatis pada PHP 5.6 dan PHP 8.2 melalui GitHub Actions untuk memastikan kompatibilitas sintaks.

## Struktur Proyek

```text
.
├── database/
│   └── schema.sql
├── uploads/
│   └── .gitkeep
├── config.example.php
├── create_admin.php
├── dashboard.php
├── detail_sop_public.php
├── edit_sop.php
├── functions.php
├── index.php
├── login.php
└── logout.php
```

## Menjalankan Secara Lokal

### Persyaratan

- PHP 5.6.40 atau versi yang lebih baru
- MySQL 5.6, MariaDB 10.1, atau versi yang lebih baru
- Ekstensi PHP `mysqli` dan `fileinfo`

### Instalasi

1. Letakkan folder proyek pada direktori web server, misalnya `htdocs` pada XAMPP.
2. Buat database dengan mengimpor `database/schema.sql` melalui phpMyAdmin atau terminal MySQL.
3. Salin `config.example.php` menjadi `config.php`.
4. Sesuaikan pengaturan database melalui variabel lingkungan atau nilai bawaan pada `config.php`.
5. Buat akun administrator dari terminal.

Linux atau macOS:

```bash
ADMIN_USERNAME=admin ADMIN_PASSWORD='kata-sandi-kuat' php create_admin.php
```

Windows PowerShell dengan XAMPP:

```powershell
$env:ADMIN_USERNAME="admin"
$env:ADMIN_PASSWORD="kata-sandi-kuat"
C:\xampp\php\php.exe create_admin.php
```

6. Buka `http://localhost/sistem-informasi-sop-dukcapil/` melalui peramban.

## Catatan Kompatibilitas

Kode ini dipertahankan agar dapat dijalankan pada XAMPP lama dengan PHP 5.6.40 untuk kebutuhan demonstrasi lokal. Untuk penggunaan daring atau lingkungan produksi, gunakan PHP 8.1 atau versi yang lebih baru karena PHP 5.6 telah berakhir masa dukungannya.

## Keamanan Data

Repositori ini tidak menyertakan akun administrator, kata sandi, data pribadi penduduk, konfigurasi lokal, maupun dokumen operasional asli. Folder `uploads` hanya menyimpan berkas pada lingkungan lokal dan isinya diabaikan oleh Git.

## Pengembang

**Micko Adrian**
S-1 Informatika, Universitas Samudra
