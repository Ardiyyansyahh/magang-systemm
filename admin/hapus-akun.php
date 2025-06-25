<?php
session_start();
include '../koneksi.php';

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Validasi metode dan data yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nim'])) {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);

    // Hapus user berdasarkan id
    $hapus = mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id' AND nim = '$nim'");

    if ($hapus) {
        header("Location: ../public/dashboard-admin.php?hapus=success");
        exit;
    } else {
        echo "Gagal menghapus akun: " . mysqli_error($koneksi);
    }
} else {
    die("Permintaan tidak valid.");
}
