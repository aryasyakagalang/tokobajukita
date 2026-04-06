<?php
// user/cart.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

// Update quantity dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['jumlah'] as $id => $qty) {
        $id  = intval($id);
        $qty = intval($qty);
        if (isset($_SESSION['cart'][$id])) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $max = $_SESSION['cart'][$id]['stok'];
                $_SESSION['cart'][$id]['jumlah'] = min($qty, $max);
            }
        }
    }
    header('Location: cart.php'); exit();
}

// Hapus satu item
if (isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    unset($_SESSION['cart'][$hapus_id]);
    header('Location: cart.php'); exit();
}

// Hitung total
$total = 0;
$cart  = $_SESSION['cart'] ?? [];
foreach ($cart as $item) {
    $total += $item['harga'] * $item['jumlah'];
}

$cart_count = 0;
foreach ($cart as $item) $cart_count += $item['jumlah'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - TokoBajuKita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="logo">Toko<span>Baju</span>Kita</a>
    <nav>
        <a href="dashboard.php">🏠 Katalog</a>
        <a href="cart.php" class="active">🛒 Keranjang <?= $cart_count > 0 ? "($cart_count)" : '' ?></a>
        <a href="pesanan_saya.php">📦 Pesanan Saya</a>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </nav>
</nav>

<div class="container">
    <h2 class="page-title">Keranjang Belanja</h2>

    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Keranjang kamu masih kosong. <a href="dashboard.php">Belanja sekarang →</a></div>
    <?php else: ?>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start;">
        <!-- Tabel Keranjang -->
        <div style="background:white; border-radius:10px; box-shadow:var(--shadow); overflow:hidden; border:1px solid var(--border);">
            <form method="POST">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart as $id => $item): ?>
                    <tr class="cart-row" data-harga="<?= $item['harga'] ?>">
                        <td><strong><?= htmlspecialchars($item['nama']) ?></strong></td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td>
                            <input type="number" name="jumlah[<?= $id ?>]"
                                   value="<?= $item['jumlah'] ?>"
                                   min="1" max="<?= $item['stok'] ?>"
                                   class="qty-input"
                                   onchange="updateTotal()"
                                   style="width:65px; padding:5px 8px; border:1px solid var(--border); border-radius:5px; text-align:center;">
                        </td>
                        <td class="subtotal"><strong>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></strong></td>
                        <td>
                            <a href="cart.php?hapus=<?= $id ?>"
                               onclick="return confirm('Hapus item ini dari keranjang?')"
                               style="color:var(--danger); font-size:1.2rem; text-decoration:none;">✕</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="padding:16px; display:flex; gap:12px; border-top:1px solid var(--border);">
                    <button type="submit" name="update_cart" class="btn btn-primary btn-sm">🔄 Update Keranjang</button>
                    <a href="dashboard.php" class="btn btn-sm" style="background:var(--bg2); color:var(--text);">← Lanjut Belanja</a>
                </div>
            </form>
        </div>

        <!-- Ringkasan Order -->
        <div class="cart-summary">
            <h3 style="font-family:'Playfair Display',serif; margin-bottom:16px; color:var(--primary);">Ringkasan Order</h3>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
                <span>Subtotal (<?= $cart_count ?> item)</span>
                <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem; color:var(--text-muted);">
                <span>Ongkos Kirim</span>
                <span>Gratis 🎉</span>
            </div>
            <hr style="border:none; border-top:1px solid var(--border); margin:12px 0;">
            <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                <strong>Total</strong>
                <span class="total" id="grand-total">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <a href="checkout.php" class="btn btn-accent btn-block" style="font-size:1rem; padding:12px;">
                🛍️ Lanjut ke Checkout
            </a>
        </div>
    </div>

    <?php endif; ?>
</div>

<script src="../js/script.js"></script>
</body>
</html>
