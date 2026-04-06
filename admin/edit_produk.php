<?php
// admin/edit_produk.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) { header('Location: produk.php'); exit(); }

// Ambil data produk
$res     = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
$product = mysqli_fetch_assoc($res);
if (!$product) { echo "Produk tidak ditemukan."; exit(); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_produk']);
    $harga     = floatval($_POST['harga']);
    $stok      = intval($_POST['stok']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar    = $product['gambar']; // Default pakai gambar lama

    if (empty($nama) || $harga <= 0 || $stok < 0) {
        $error = "Nama, harga, dan stok harus diisi dengan benar!";
    } else {
        // Upload gambar baru jika ada
        if (!empty($_FILES['gambar']['name'])) {
            $ext_ok = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext    = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $ext_ok)) {
                $error = "Format gambar tidak valid!";
            } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
                $error = "Ukuran gambar maksimal 2MB!";
            } else {
                $gambar_baru = time() . '_' . basename($_FILES['gambar']['name']);
                $target      = '../uploads/' . $gambar_baru;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    // Hapus gambar lama
                    if ($product['gambar'] && file_exists('../uploads/' . $product['gambar'])) {
                        unlink('../uploads/' . $product['gambar']);
                    }
                    $gambar = $gambar_baru;
                } else {
                    $error = "Gagal upload gambar baru!";
                }
            }
        }

        if (empty($error)) {
            $sql  = "UPDATE products SET nama_produk=?, harga=?, stok=?, deskripsi=?, gambar=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sdissi", $nama, $harga, $stok, $deskripsi, $gambar, $product_id);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: produk.php?status=updated'); exit();
            } else {
                $error = "Gagal memperbarui produk!";
            }
        }
    }

    // Refresh data setelah submit (untuk tampil form terbaru)
    $res     = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
    $product = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-content">
        <h2 class="page-title">Edit Produk</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="background:white; padding:28px; border-radius:10px; box-shadow:var(--shadow); max-width:600px; border:1px solid var(--border);">
            <form method="POST" enctype="multipart/form-data" onsubmit="return validasiFormProduk()">
                <div class="form-group">
                    <label>Nama Produk *</label>
                    <input type="text" id="nama_produk" name="nama_produk" required value="<?= htmlspecialchars($product['nama_produk']) ?>">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Harga (Rp) *</label>
                        <input type="number" id="harga" name="harga" min="1" required value="<?= $product['harga'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Stok *</label>
                        <input type="number" id="stok" name="stok" min="0" required value="<?= $product['stok'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi"><?= htmlspecialchars($product['deskripsi']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Ganti Gambar (kosongkan jika tidak ingin ganti)</label>
                    <?php if ($product['gambar'] && file_exists('../uploads/' . $product['gambar'])): ?>
                        <p style="font-size:0.85rem; color:#888; margin-bottom:8px;">Gambar saat ini:</p>
                        <img src="../uploads/<?= htmlspecialchars($product['gambar']) ?>" style="max-height:120px; border-radius:6px; margin-bottom:10px; border:1px solid var(--border);">
                    <?php endif; ?>
                    <input type="file" name="gambar" accept="image/*" onchange="previewGambar(this)">
                    <img id="img-preview" src="" style="display:none; margin-top:10px; max-height:150px; border-radius:6px; border:1px solid var(--border);">
                </div>
                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
                    <a href="produk.php" class="btn btn-primary">← Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
