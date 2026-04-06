<?php
// includes/koneksi.php
// Konfigurasi koneksi database

$host = "localhost";
$user = "root";        // Ganti dengan username database kamu
$pass = "";            // Ganti dengan password database kamu
$db   = "db_jualbaju";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
