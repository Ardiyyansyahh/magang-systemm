<?php
session_start();
include '../koneksi.php';

$mahasiswa_id = $_SESSION['user_id'] ?? null;
$perusahaan   = $_POST['perusahaan'];
$alamat       = $_POST['alamat'];
$divisi       = $_POST['divisi'];

$query = "INSERT INTO pendaftaran_magang (mahasiswa_id, perusahaan, alamat, divisi, status)
          VALUES ('$mahasiswa_id', '$perusahaan', '$alamat', '$divisi', 'pending')";

if (mysqli_query($koneksi, $query)) {
    header("Location: ../public/dashboard-mahasiswa.html?daftar=berhasil");
} else {
    echo "Gagal mendaftar: " . mysqli_error($koneksi);
}
?>
