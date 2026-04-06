<?php
// admin/pesanan.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

// Update status pesanan
if (isset($_POST['update_status'])) {
    $order_id  = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    $allowed    = ['pending', 'dikirim', 'selesai'];
    if (in_array($new_status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$new_status' WHERE id=$order_id");
    }
}

$pesanan = mysqli_query($conn, "
    SELECT o.*, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-content">
        <h2 class="page-title">Manajemen Pesanan</h2>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th><th>Pelanggan</th><th>Alamat</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($pesanan) > 0):
                    while ($row = mysqli_fetch_assoc($pesanan)): ?>
                    <tr>
                        <td><strong>#<?= $row['id'] ?></strong></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td style="max-width:180px; font-size:0.82rem;"><?= htmlspecialchars(substr($row['alamat'], 0, 60)) ?>...</td>
                        <td><strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td style="font-size:0.82rem;"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <select name="new_status" style="padding:5px 8px; border:1px solid var(--border); border-radius:5px; font-size:0.82rem;">
                                    <option value="pending"  <?= $row['status'] === 'pending'  ? 'selected' : '' ?>>Pending</option>
                                    <option value="dikirim"  <?= $row['status'] === 'dikirim'  ? 'selected' : '' ?>>Dikirim</option>
                                    <option value="selesai"  <?= $row['status'] === 'selesai'  ? 'selected' : '' ?>>Selesai</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-success">✓</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="7" style="text-align:center; color:#aaa; padding:30px;">Belum ada pesanan masuk.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
