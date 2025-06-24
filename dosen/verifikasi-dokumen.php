<?php
session_start();
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar']);
    $status = $_POST['status'];

    $query = "UPDATE dokumen_magang 
              SET komentar_dosen='$komentar', status_verifikasi='$status'
              WHERE id=$id";
    mysqli_query($koneksi, $query);
}

header("Location: ../public/dashboard-dosen.php");
exit;
?>
