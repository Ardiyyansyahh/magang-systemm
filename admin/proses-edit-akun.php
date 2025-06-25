<?php
session_start();
include '../koneksi.php';

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Pastikan request-nya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode tidak valid.");
}

// Ambil dan validasi ID
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    die("ID tidak valid.");
}

// Amankan input
$nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
$email    = trim(mysqli_real_escape_string($koneksi, $_POST['email'] ?? ''));
$role     = $_POST['role'] ?? '';
$nim      = trim(mysqli_real_escape_string($koneksi, $_POST['nim'] ?? ''));
$angkatan = trim(mysqli_real_escape_string($koneksi, $_POST['angkatan'] ?? ''));
$fakultas = trim(mysqli_real_escape_string($koneksi, $_POST['fakultas'] ?? ''));
$bidang_keahlian = trim(mysqli_real_escape_string($koneksi, $_POST['bidang_keahlian'] ?? ''));

// Validasi peran
$allowed_roles = ['mahasiswa', 'dosen'];
if (!in_array($role, $allowed_roles)) {
    die("Peran tidak valid.");
}

// Susun query berdasarkan peran
if ($role === 'mahasiswa') {
    $query = "UPDATE users SET 
                nama = '$nama',
                email = '$email',
                role = '$role',
                nim = '$nim',
                angkatan = '$angkatan',
                fakultas = '$fakultas',
                bidang_keahlian = NULL
              WHERE id = $id";
} else {
    $query = "UPDATE users SET 
                nama = '$nama',
                email = '$email',
                role = '$role',
                nim = NULL,
                angkatan = NULL,
                fakultas = '$fakultas',
                bidang_keahlian = '$bidang_keahlian'
              WHERE id = $id";
}

// Jalankan dan arahkan
if (mysqli_query($koneksi, $query)) {
    header("Location: ../public/dashboard-admin.php?edit=success");
    exit;
} else {
    echo "Gagal mengupdate data: " . mysqli_error($koneksi);
}
?>
