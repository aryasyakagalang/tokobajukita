<?php // admin/includes/navbar.php ?>
<nav class="navbar">
    <a href="dashboard.php" class="logo">Toko<span>Baju</span>Kita</a>
    <nav>
        <span style="color:#aaa; font-size:0.88rem;">👤 Admin: <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </nav>
</nav>
