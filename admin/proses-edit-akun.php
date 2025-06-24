<?php
session_start();
include '../koneksi.php';

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Validasi metode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode tidak valid.");
}

// Ambil dan validasi data
$id    = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$role  = $_POST['role'];

$allowed_roles = ['mahasiswa', 'dosen'];
if (!in_array($role, $allowed_roles)) {
    die("Peran tidak valid.");
}

// Update data
$query = "UPDATE users SET nama = '$nama', email = '$email', role = '$role' WHERE id = $id";
if (mysqli_query($koneksi, $query)) {
    header("Location: ../public/dashboard-admin.php?edit=success");
    exit;
} else {
    echo "Gagal mengupdate data: " . mysqli_error($koneksi);
}
?>
