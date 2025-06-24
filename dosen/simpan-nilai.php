<?php
session_start();
include '../koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['message' => 'Akses ditolak.']);
    exit;
}

$id = $_POST['id_laporan'];
$nilai = $_POST['nilai'];
$komentar = $_POST['komentar'];

$query = "UPDATE laporan_mingguan 
          SET nilai = '$nilai', komentar = '$komentar'
          WHERE id = $id";

if (mysqli_query($koneksi, $query)) {
    echo json_encode(['success' => true, 'message' => 'Data berhasil diperbarui.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan.']);
}
