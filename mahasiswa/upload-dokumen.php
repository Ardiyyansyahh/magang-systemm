<?php
session_start();
include '../koneksi.php';

// Validasi login
$mahasiswa_id = $_SESSION['user_id'] ?? null;
if (!$mahasiswa_id) {
    die("Akses ditolak. Harap login.");
}

// Validasi form
if (!isset($_FILES['proposal']) || $_FILES['proposal']['error'] !== 0) {
    die("File belum diunggah atau terjadi kesalahan.");
}

// Buat direktori upload jika belum ada
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Validasi file proposal
$proposal = $_FILES['proposal'];
$extProposal = strtolower(pathinfo($proposal['name'], PATHINFO_EXTENSION));

if ($extProposal !== 'pdf') {
    die("Hanya file PDF yang diperbolehkan.");
}

// Simpan file
$proposalName = uniqid('proposal_') . '.pdf';
$proposalPath = $uploadDir . $proposalName;

if (!move_uploaded_file($proposal['tmp_name'], $proposalPath)) {
    die("Gagal menyimpan file.");
}

// Simpan ke database
$judul = mysqli_real_escape_string($koneksi, $_POST['judul'] ?? 'Dokumen Magang');
$query = "INSERT INTO dokumen_magang (mahasiswa_id, judul, file_path)
          VALUES ('$mahasiswa_id', '$judul', '$proposalName')";

if (mysqli_query($koneksi, $query)) {
    header("Location: ../public/dashboard-mahasiswa.php?upload=success");
    exit;
} else {
    echo "Gagal menyimpan ke database: " . mysqli_error($koneksi);
}
?>