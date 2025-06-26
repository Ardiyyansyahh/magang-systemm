<?php
session_start();
include '../koneksi.php';

// Cek apakah user admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Pastikan metode POST dan ID ada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Eksekusi query penghapusan
    $hapus = mysqli_query($koneksi, "DELETE FROM perusahaan_mitra WHERE id = $id");

    if ($hapus) {
        header("Location: ../public/dashboard-admin.php?hapus_perusahaan=success");
        exit;
    } else {
        echo "Gagal menghapus perusahaan: " . mysqli_error($koneksi);
    }
} else {
    die("Permintaan tidak valid.");
}
?>
