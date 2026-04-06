<?php
// user/tambah_cart.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php'); exit();
}
include_once '../includes/koneksi.php';

$product_id = intval($_POST['product_id'] ?? 0);
$redirect   = $_POST['redirect'] ?? 'dashboard.php';

if ($product_id > 0) {
    // Cek produk di database
    $res  = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id AND stok > 0");
    $prod = mysqli_fetch_assoc($res);

    if ($prod) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            // Sudah ada di keranjang, tambah jumlah (max = stok)
            $new_qty = $_SESSION['cart'][$product_id]['jumlah'] + 1;
            if ($new_qty <= $prod['stok']) {
                $_SESSION['cart'][$product_id]['jumlah'] = $new_qty;
            }
        } else {
            // Tambah baru ke keranjang
            $_SESSION['cart'][$product_id] = [
                'nama'  => $prod['nama_produk'],
                'harga' => $prod['harga'],
                'jumlah'=> 1,
                'stok'  => $prod['stok'],
            ];
        }
        header('Location: ' . $redirect . '?msg=added');
    } else {
        header('Location: ' . $redirect . '?msg=error');
    }
} else {
    header('Location: dashboard.php');
}
exit();
?>
