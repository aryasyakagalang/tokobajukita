<?php
// admin/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

// Statistik
$total_produk  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_user    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='user'"))[0];
$total_order   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$order_pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE status='pending'"))[0];

// 5 pesanan terbaru
$pesanan = mysqli_query($conn, "
    SELECT o.id, u.username, o.total_harga, o.status, o.created_at
    FROM orders o JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - TokoBajuKita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-content">
        <h2 class="page-title">Dashboard</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-num"><?= $total_produk ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
            <div class="stat-card" style="border-left-color:#27ae60">
                <div class="stat-num"><?= $total_user ?></div>
                <div class="stat-label">Total Pelanggan</div>
            </div>
            <div class="stat-card" style="border-left-color:#2980b9">
                <div class="stat-num"><?= $total_order ?></div>
                <div class="stat-label">Total Pesanan</div>
            </div>
            <div class="stat-card" style="border-left-color:#e74c3c">
                <div class="stat-num"><?= $order_pending ?></div>
                <div class="stat-label">Pesanan Pending</div>
            </div>
        </div>

        <h3 style="font-family:'Playfair Display',serif; color:var(--primary); margin-bottom:10px;">Pesanan Terbaru</h3>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($pesanan) > 0):
                    while ($row = mysqli_fetch_assoc($pesanan)): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td><a href="pesanan.php" class="btn btn-sm btn-primary">Lihat Semua</a></td>
                    </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="6" style="text-align:center; color:#aaa; padding:24px;">Belum ada pesanan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
