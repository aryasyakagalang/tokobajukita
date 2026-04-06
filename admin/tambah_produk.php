<?php
// admin/tambah_produk.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama_produk']);
    $harga    = floatval($_POST['harga']);
    $stok     = intval($_POST['stok']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar   = '';

    if (empty($nama) || $harga <= 0 || $stok < 0) {
        $error = "Nama produk, harga, dan stok wajib diisi dengan benar!";
    } else {
        // Upload gambar jika ada
        if (!empty($_FILES['gambar']['name'])) {
            $ext_ok  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext     = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $ext_ok)) {
                $error = "Format gambar tidak valid! Gunakan JPG, PNG, atau GIF.";
            } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
                $error = "Ukuran gambar maksimal 2MB!";
            } else {
                $gambar      = time() . '_' . basename($_FILES['gambar']['name']);
                $target_file = '../uploads/' . $gambar;
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $error  = "Gagal mengupload gambar!";
                    $gambar = '';
                }
            }
        }

        if (empty($error)) {
            $sql  = "INSERT INTO products (nama_produk, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sdiss", $nama, $harga, $stok, $deskripsi, $gambar);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: produk.php?status=added'); exit();
            } else {
                $error = "Gagal menyimpan produk!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-content">
        <h2 class="page-title">Tambah Produk Baru</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:white; padding:28px; border-radius:10px; box-shadow:var(--shadow); max-width:600px; border:1px solid var(--border);">
            <form method="POST" enctype="multipart/form-data" onsubmit="return validasiFormProduk()">
                <div class="form-group">
                    <label>Nama Produk *</label>
                    <input type="text" id="nama_produk" name="nama_produk" placeholder="Contoh: Kaos Polos Putih" required value="<?= htmlspecialchars($_POST['nama_produk'] ?? '') ?>">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Harga (Rp) *</label>
                        <input type="number" id="harga" name="harga" placeholder="75000" min="1" required value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Stok *</label>
                        <input type="number" id="stok" name="stok" placeholder="50" min="0" required value="<?= htmlspecialchars($_POST['stok'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" placeholder="Deskripsikan produk ini..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Gambar Produk (opsional, maks 2MB)</label>
                    <input type="file" name="gambar" accept="image/*" onchange="previewGambar(this)">
                    <img id="img-preview" src="" style="display:none; margin-top:10px; max-height:150px; border-radius:6px; border:1px solid var(--border);">
                </div>
                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="submit" class="btn btn-accent">💾 Simpan Produk</button>
                    <a href="produk.php" class="btn btn-primary">← Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
