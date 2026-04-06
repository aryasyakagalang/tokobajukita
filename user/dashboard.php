<?php
// user/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

// Pagination
$per_page   = 6;
$page       = max(1, intval($_GET['page'] ?? 1));
$offset     = ($page - 1) * $per_page;

$total_row  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE stok > 0"))[0];
$total_page = ceil($total_row / $per_page);

$produk = mysqli_query($conn, "SELECT * FROM products WHERE stok > 0 ORDER BY id DESC LIMIT $per_page OFFSET $offset");

// Jumlah item di keranjang
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
    <title>Katalog Produk - TokoBajuKita</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="dashboard.php" class="logo">Toko<span>Baju</span>Kita</a>
    <nav>
        <a href="dashboard.php" class="active">🏠 Katalog</a>
        <a href="cart.php">🛒 Keranjang <?= $cart_count > 0 ? "($cart_count)" : '' ?></a>
        <a href="pesanan_saya.php">📦 Pesanan Saya</a>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </nav>
</nav>

<!-- Hero -->
<div class="hero">
    <h1>Selamat datang, <span><?= htmlspecialchars($_SESSION['username']) ?>!</span></h1>
    <p>Temukan koleksi baju terbaik dengan harga terjangkau 🛍️</p>
</div>

<div class="container">
    <h2 class="page-title">Katalog Produk</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
        <div class="alert alert-success">Produk berhasil ditambahkan ke keranjang!</div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($produk) > 0): ?>
    <div class="product-grid">
        <?php while ($row = mysqli_fetch_assoc($produk)): ?>
        <div class="product-card">
            <div class="card-img">
                <?php if ($row['gambar'] && file_exists('../uploads/' . $row['gambar'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                <?php else: ?>
                    👕
                <?php endif; ?>
            </div>
            <div class="card-body">
                <h3><?= htmlspecialchars($row['nama_produk']) ?></h3>
                <div class="price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                <div class="stok">Stok: <?= $row['stok'] ?> pcs</div>
                <div class="desc"><?= htmlspecialchars(substr($row['deskripsi'], 0, 80)) ?>...</div>
                <form method="POST" action="tambah_cart.php">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="redirect" value="dashboard.php">
                    <button type="submit" class="btn-cart">🛒 Tambah ke Keranjang</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_page > 1): ?>
    <div style="display:flex; justify-content:center; gap:8px; margin-top:32px;">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <a href="?page=<?= $i ?>" class="btn <?= $i === $page ? 'btn-accent' : 'btn-primary' ?>" style="padding:8px 15px;"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">Belum ada produk yang tersedia saat ini.</div>
    <?php endif; ?>
</div>

<script src="../js/script.js"></script>
</body>
</html>
