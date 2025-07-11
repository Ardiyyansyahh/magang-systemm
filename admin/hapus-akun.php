<?php
session_start();
include '../koneksi.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Validasi metode dan input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Hapus dokumen yang berkaitan terlebih dahulu
    mysqli_query($koneksi, "DELETE FROM dokumen_magang WHERE mahasiswa_id = $id");

    // Kemudian hapus dari tabel users
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