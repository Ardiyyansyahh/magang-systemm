<?php
session_start();
include '../koneksi.php';

// Cek apakah pengguna login dan berperan sebagai dosen
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../login.html");
    exit;
}

// Cek apakah data dikirim via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan validasi input
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = trim($_POST['status'] ?? '');
    $komentar = trim($_POST['komentar'] ?? '');

    // Validasi status
    $allowed = ['Menunggu', 'Disetujui', 'Ditolak'];
    if (!in_array($status, $allowed)) {
        header("Location: ../public/dashboard-dosen.php?error=status_tidak_valid");
        exit;
    }

    // Escape input komentar untuk mencegah SQL injection
    $komentar = mysqli_real_escape_string($koneksi, $komentar);

    // Lakukan update ke database
    $query = "UPDATE dokumen_magang 
              SET status_verifikasi = '$status', komentar_dosen = '$komentar'
              WHERE id = $id";

    if (mysqli_query($koneksi, $query)) {
        header("Location: ../public/dashboard-dosen.php?status=updated");
        exit;
    } else {
        header("Location: ../public/dashboard-dosen.php?error=gagal_update");
        exit;
    }
} else {
    header("Location: ../public/dashboard-dosen.php?error=akses_tidak_sah");
    exit;
}
