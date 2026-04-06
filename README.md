# TokoBajuKita 👕
Website toko baju online berbasis PHP Native + MySQL — Proyek Sekolah

---

## 📁 Struktur Folder

```
tokobaju/
├── index.php              ← Halaman login & registrasi
├── logout.php             ← Proses logout
├── db_jualbaju.sql        ← File database (import ini dulu!)
├── css/
│   └── style.css          ← Stylesheet utama
├── js/
│   └── script.js          ← JavaScript interaksi
├── includes/
│   └── koneksi.php        ← Koneksi database
├── uploads/               ← Folder gambar produk
├── admin/
│   ├── dashboard.php      ← Dashboard admin
│   ├── produk.php         ← Daftar produk (CRUD)
│   ├── tambah_produk.php  ← Form tambah produk
│   ├── edit_produk.php    ← Form edit produk
│   ├── delete_produk.php  ← Hapus produk
│   ├── pesanan.php        ← Manajemen pesanan
│   └── includes/
│       ├── navbar.php
│       └── sidebar.php
└── user/
    ├── dashboard.php      ← Katalog produk
    ├── cart.php           ← Keranjang belanja
    ├── tambah_cart.php    ← Proses tambah ke keranjang
    ├── checkout.php       ← Form checkout
    └── pesanan_saya.php   ← Riwayat pesanan user
```

---

## ⚙️ Cara Setup

### 1. Persiapan
- Pastikan sudah install **XAMPP** (PHP + MySQL)
- Letakkan folder `tokobaju/` di dalam `C:/xampp/htdocs/`

### 2. Import Database
1. Buka browser → `http://localhost/phpmyadmin`
2. Klik **"New"** → buat database bernama `db_jualbaju`
3. Klik tab **"Import"** → pilih file `db_jualbaju.sql` → klik **Go**

### 3. Sesuaikan Koneksi (jika perlu)
Edit file `includes/koneksi.php`:
```php
$host = "localhost";
$user = "root";   // username MySQL kamu
$pass = "";       // password MySQL kamu (kosong jika pakai XAMPP default)
$db   = "db_jualbaju";
```

### 4. Jalankan Website
Buka browser → `http://localhost/tokobaju/`

---

## 🔑 Akun Demo

| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | password |
| User  | user1    | password |

---

## ✨ Fitur Website

- ✅ Login & Registrasi (dengan password hash bcrypt)
- ✅ Dua role: Admin & User
- ✅ CRUD Produk (Create, Read, Update, Delete)
- ✅ Upload gambar produk
- ✅ Katalog produk dengan pagination
- ✅ Keranjang belanja dinamis (session)
- ✅ Update qty real-time dengan JavaScript
- ✅ Proses checkout & simpan pesanan
- ✅ Manajemen status pesanan (Admin)
- ✅ Riwayat pesanan (User)
- ✅ Desain responsif (mobile-friendly)
- ✅ Proteksi SQL Injection (prepared statement)

---

## 🚀 Upload ke GitHub

```bash
# 1. Masuk ke folder proyek
cd /path/to/tokobaju

# 2. Inisialisasi Git
git init

# 3. Konfigurasi identitas
git config --global user.name "Nama Kamu"
git config --global user.email "email@example.com"

# 4. Tambah semua file
git add .

# 5. Commit pertama
git commit -m "Initial commit - Website Toko Baju"

# 6. Hubungkan ke GitHub (ganti URL sesuai repo kamu)
git remote add origin https://github.com/USERNAME/tokobaju.git

# 7. Set branch utama dan push
git branch -M main
git push -u origin main
```

> ⚠️ **Catatan:** Jangan lupa tambahkan file `.gitignore` dan jangan upload file `koneksi.php` yang berisi password asli ke GitHub publik!

---

## 🛠️ Teknologi

- **Backend:** PHP Native (tanpa framework)
- **Database:** MySQL dengan MySQLi
- **Frontend:** HTML, CSS, JavaScript (vanilla)
- **Server:** XAMPP (Apache + MySQL)
- **Versi Kontrol:** Git & GitHub
