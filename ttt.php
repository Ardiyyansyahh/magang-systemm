<?php
session_start();
include '../koneksi.php';

// Pastikan user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Validasi metode & input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $hapus = mysqli_query($koneksi, "DELETE FROM users WHERE id = $id");

    if ($hapus) {
        header("Location: ../public/dashboard-admin.php?hapus=success");
        exit;
    } else {
        echo "Gagal menghapus akun: " . mysqli_error($koneksi);
    }
} else {
    die("Permintaan tidak valid.");
}
?>