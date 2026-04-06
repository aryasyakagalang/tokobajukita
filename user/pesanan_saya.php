<?php
// user/pesanan_saya.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

$user_id = $_SESSION['user_id'];
$pesanan = mysqli_query($conn, "
    SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC
");

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $cart_count += $item['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - TokoBajuKita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="logo">Toko<span>Baju</span>Kita</a>
    <nav>
        <a href="dashboard.php">🏠 Katalog</a>
        <a href="cart.php">🛒 Keranjang <?= $cart_count > 0 ? "($cart_count)" : '' ?></a>
        <a href="pesanan_saya.php" class="active">📦 Pesanan Saya</a>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </nav>
</nav>

<div class="container">
    <h2 class="page-title">Pesanan Saya</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div class="alert alert-success">✅ Pesanan berhasil dibuat! Kami akan segera memproses pesananmu.</div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($pesanan) > 0): ?>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#ID</th><th>Total</th><th>Alamat</th><th>Status</th><th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
            <tr>
                <td><strong>#<?= $row['id'] ?></strong></td>
                <td><strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong></td>
                <td style="max-width:200px; font-size:0.85rem;"><?= htmlspecialchars(substr($row['alamat'], 0, 60)) ?>...</td>
                <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                <td style="font-size:0.85rem;"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info">
            Kamu belum punya pesanan. <a href="dashboard.php" style="font-weight:600;">Mulai belanja sekarang →</a>
        </div>
    <?php endif; ?>
</div>
<script src="../js/script.js"></script>
</body>
</html>
