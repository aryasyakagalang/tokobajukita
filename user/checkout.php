<?php
// user/checkout.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: dashboard.php'); exit();
}

$total = 0;
foreach ($cart as $item) $total += $item['harga'] * $item['jumlah'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat = trim($_POST['alamat']);
    if (empty($alamat)) {
        $error = "Alamat pengiriman tidak boleh kosong!";
    } else {
        // Simpan order
        $user_id = $_SESSION['user_id'];
        $stmt    = mysqli_prepare($conn, "INSERT INTO orders (user_id, alamat, total_harga, status) VALUES (?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, "isd", $user_id, $alamat, $total);

        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($conn);

            // Simpan order items & kurangi stok
            foreach ($cart as $product_id => $item) {
                $si = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, jumlah, harga) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($si, "iiid", $order_id, $product_id, $item['jumlah'], $item['harga']);
                mysqli_stmt_execute($si);

                // Kurangi stok
                mysqli_query($conn, "UPDATE products SET stok = stok - {$item['jumlah']} WHERE id = $product_id AND stok >= {$item['jumlah']}");
            }

            // Kosongkan keranjang
            $_SESSION['cart'] = [];
            header('Location: pesanan_saya.php?msg=success'); exit();
        } else {
            $error = "Gagal memproses pesanan, coba lagi!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TokoBajuKita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="logo">Toko<span>Baju</span>Kita</a>
    <nav>
        <a href="dashboard.php">🏠 Katalog</a>
        <a href="cart.php">🛒 Keranjang</a>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </nav>
</nav>

<div class="container">
    <h2 class="page-title">Checkout</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start;">

        <!-- Form Pengiriman -->
        <div style="background:white; padding:28px; border-radius:10px; box-shadow:var(--shadow); border:1px solid var(--border);">
            <h3 style="font-family:'Playfair Display',serif; margin-bottom:20px; color:var(--primary);">📍 Informasi Pengiriman</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Penerima</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['username']) ?>" disabled style="background:#f5f5f5; color:#888;">
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap *</label>
                    <textarea name="alamat" placeholder="Contoh: Jl. Merdeka No. 10, Kelurahan Karang Anyar, Kecamatan Gubeng, Surabaya 60281" required style="min-height:120px;"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%; padding:12px; font-size:1rem;">
                    ✅ Konfirmasi Pesanan
                </button>
                <a href="cart.php" style="display:block; text-align:center; margin-top:12px; color:var(--text-muted); font-size:0.88rem;">← Kembali ke Keranjang</a>
            </form>
        </div>

        <!-- Ringkasan Belanja -->
        <div style="background:white; padding:24px; border-radius:10px; box-shadow:var(--shadow); border:1px solid var(--border);">
            <h3 style="font-family:'Playfair Display',serif; margin-bottom:16px; color:var(--primary);">🛍️ Detail Pesanan</h3>
            <?php foreach ($cart as $item): ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.88rem; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <span><?= htmlspecialchars($item['nama']) ?> × <?= $item['jumlah'] ?></span>
                <strong>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></strong>
            </div>
            <?php endforeach; ?>
            <div style="display:flex; justify-content:space-between; margin-top:12px; font-size:0.88rem; color:var(--text-muted);">
                <span>Ongkos Kirim</span><span>Gratis</span>
            </div>
            <hr style="border:none; border-top:1px solid var(--border); margin:12px 0;">
            <div style="display:flex; justify-content:space-between;">
                <strong>TOTAL</strong>
                <strong style="color:var(--accent2); font-size:1.15rem;">Rp <?= number_format($total, 0, ',', '.') ?></strong>
            </div>
        </div>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
