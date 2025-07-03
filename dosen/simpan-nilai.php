<?php
session_start();
include '../koneksi.php';

// Cek apakah dosen sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laporan_id = (int) $_POST['laporan_id'];
    $nilai = (int) $_POST['nilai'];
    $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar']);

    // Validasi input
    if ($nilai < 0 || $nilai > 100) {
        header("Location: ../public/dashboard-dosen.php?error=nilai_invalid");
        exit;
    }

    // Update laporan dengan nilai dan komentar
    $query = "UPDATE laporan_mingguan SET nilai = $nilai, komentar = '$komentar' WHERE id = $laporan_id";

    if (mysqli_query($koneksi, $query)) {
        header("Location: ../public/dashboard-dosen.php?success=nilai_tersimpan");
    } else {
        header("Location: ../public/dashboard-dosen.php?error=gagal_simpan");
    }
} else {
    header("Location: ../public/dashboard-dosen.php");
}
?>