<?php
// admin/delete_produk.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Ambil nama gambar sebelum dihapus
    $res  = mysqli_query($conn, "SELECT gambar FROM products WHERE id = $product_id");
    $row  = mysqli_fetch_assoc($res);

    $sql  = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        // Hapus file gambar dari server jika ada
        if ($row && $row['gambar'] && file_exists('../uploads/' . $row['gambar'])) {
            unlink('../uploads/' . $row['gambar']);
        }
        header('Location: produk.php?status=deleted');
    } else {
        header('Location: produk.php?status=error');
    }
    exit();
} else {
    header('Location: produk.php');
    exit();
}
?>
