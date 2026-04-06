<?php
// index.php - Halaman Login & Registrasi
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}

include_once 'includes/koneksi.php';

$error   = '';
$success = '';
$tab     = 'login'; // tab aktif default

// ============ PROSES LOGIN ============
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();
        $stmt->close();

        if ($id && password_verify($password, $hashed_password)) {
            $_SESSION['user_id']  = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role']     = $role;

            if ($role === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    }
}

// ============ PROSES REGISTRASI ============
if (isset($_POST['register'])) {
    $tab             = 'register';
    $username        = trim($_POST['reg_username']);
    $password        = $_POST['reg_password'];
    $confirm         = $_POST['reg_confirm'];

    if (empty($username) || empty($password)) {
        $error = "Semua field wajib diisi!";
    } elseif (strlen($username) < 3) {
        $error = "Username minimal 3 karakter!";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $error = "Username hanya boleh huruf, angka, dan underscore!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        // Cek username sudah ada
        $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan, pilih yang lain!";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $ins    = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $ins->bind_param("ss", $username, $hashed);

            if ($ins->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
                $tab     = 'login';
            } else {
                $error = "Gagal mendaftar, coba lagi.";
            }
            $ins->close();
        }
        $cek->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Baju Kita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="logo-title">Toko<span>Baju</span>Kita</div>
        <p class="subtitle">Belanja baju terbaik dengan harga terjangkau</p>

        <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <div class="login-tabs">
            <button onclick="showTab('login')"    class="<?= $tab === 'login'    ? 'active' : '' ?>">Login</button>
            <button onclick="showTab('register')" class="<?= $tab === 'register' ? 'active' : '' ?>">Daftar</button>
        </div>

        <!-- Form Login -->
        <div id="form-login" style="display: <?= $tab === 'login' ? 'block' : 'none' ?>">
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
            </form>
            <p style="text-align:center; margin-top:16px; font-size:0.85rem; color:#888;">
                Belum punya akun? <a href="#" onclick="showTab('register')" style="color:#1a1a2e; font-weight:600;">Daftar di sini</a>
            </p>
            <p style="text-align:center; margin-top:8px; font-size:0.78rem; color:#bbb;">
                Demo: admin / password &nbsp;|&nbsp; user1 / password
            </p>
        </div>

        <!-- Form Registrasi -->
        <div id="form-register" style="display: <?= $tab === 'register' ? 'block' : 'none' ?>">
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="reg_username" placeholder="Buat username unik" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="reg_password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="reg_confirm" placeholder="Ulangi password" required>
                </div>
                <button type="submit" name="register" class="btn btn-accent btn-block">Daftar Sekarang</button>
            </form>
            <p style="text-align:center; margin-top:16px; font-size:0.85rem; color:#888;">
                Sudah punya akun? <a href="#" onclick="showTab('login')" style="color:#1a1a2e; font-weight:600;">Login di sini</a>
            </p>
        </div>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
