<?php
// admin/produk.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

$status_msg = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'deleted')  $status_msg = '<div class="alert alert-success">Produk berhasil dihapus.</div>';
    if ($_GET['status'] === 'added')    $status_msg = '<div class="alert alert-success">Produk berhasil ditambahkan.</div>';
    if ($_GET['status'] === 'updated')  $status_msg = '<div class="alert alert-success">Produk berhasil diperbarui.</div>';
}

$produk = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 class="page-title" style="margin-bottom:0;">Daftar Produk</h2>
            <a href="tambah_produk.php" class="btn btn-accent">+ Tambah Produk</a>
        </div>

        <?= $status_msg ?>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>No</th><th>Gambar</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($produk) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($produk)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <?php if ($row['gambar'] && file_exists('../uploads/' . $row['gambar'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" style="width:55px; height:55px; object-fit:cover; border-radius:6px;">
                            <?php else: ?>
                                <div style="width:55px; height:55px; background:#f0ede8; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">👕</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($row['nama_produk']) ?></strong>
                            <br><small style="color:#aaa;"><?= htmlspecialchars(substr($row['deskripsi'], 0, 50)) ?>...</small>
                        </td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td>
                            <span style="color: <?= $row['stok'] < 5 ? '#e74c3c' : '#27ae60' ?>; font-weight:600;">
                                <?= $row['stok'] ?>
                            </span>
                        </td>
                        <td style="display:flex; gap:8px; align-items:center; padding-top:18px;">
                            <a href="edit_produk.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Edit</a>
                            <button onclick="konfirmasiHapus('delete_produk.php?id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nama_produk']) ?>')" class="btn btn-sm btn-danger">Hapus</button>
                        </td>
                    </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="6" style="text-align:center; color:#aaa; padding:30px;">Belum ada produk. <a href="tambah_produk.php">Tambah sekarang</a></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
