<?php // admin/includes/sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="menu-title">Menu</div>
    <a href="dashboard.php"      class="<?= $current === 'dashboard.php'      ? 'active' : '' ?>">📊 Dashboard</a>
    <a href="produk.php"         class="<?= $current === 'produk.php'         ? 'active' : '' ?>">👕 Produk</a>
    <a href="tambah_produk.php"  class="<?= $current === 'tambah_produk.php'  ? 'active' : '' ?>">➕ Tambah Produk</a>
    <a href="pesanan.php"        class="<?= $current === 'pesanan.php'        ? 'active' : '' ?>">📦 Pesanan</a>
    <div class="menu-title">Akun</div>
    <a href="../logout.php">🚪 Logout</a>
</aside>
