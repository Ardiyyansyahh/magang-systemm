<?php
// update-status-pendaftaran.php
include '../koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pendaftaran_id = $_POST['pendaftaran_id'];
    $status = $_POST['status'];

    $query = "UPDATE pendaftaran_magang SET status = '$status' WHERE id = $pendaftaran_id";

    if (mysqli_query($koneksi, $query)) {
        header("Location: /public/dashboard-dosen.php?msg=success");
    } else {
        echo "Gagal memperbarui status: " . mysqli_error($koneksi);
    }
} else {
    echo "Metode tidak diizinkan";
}
