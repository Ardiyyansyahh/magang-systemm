<?php
session_start();
include '../koneksi.php';

if (!isset($_FILES['proposal']) || !isset($_FILES['surat'])) {
    die("File belum diunggah.");
}

$mahasiswa_id = $_SESSION['user_id'] ?? null;
if (!$mahasiswa_id) {
    die("Akses ditolak. Harap login.");
}

$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$proposal = $_FILES['proposal'];
$surat    = $_FILES['surat'];

// Validasi: hanya file .pdf (berdasarkan ekstensi)
$extProposal = strtolower(pathinfo($proposal['name'], PATHINFO_EXTENSION));
$extSurat    = strtolower(pathinfo($surat['name'], PATHINFO_EXTENSION));

if ($extProposal !== 'pdf' || $extSurat !== 'pdf') {
    die("Hanya file dengan ekstensi .pdf yang diperbolehkan.");
}

$proposalName = uniqid('proposal_') . '.pdf';
$suratName    = uniqid('surat_') . '.pdf';

$proposalPath = $uploadDir . $proposalName;
$suratPath    = $uploadDir . $suratName;

if (!move_uploaded_file($proposal['tmp_name'], $proposalPath) ||
    !move_uploaded_file($surat['tmp_name'], $suratPath)) {
    die("Gagal menyimpan file.");
}

$judul = mysqli_real_escape_string($koneksi, "Dokumen Magang Mahasiswa");
$filePaths = "$proposalName, $suratName";

$query = "INSERT INTO dokumen_magang (mahasiswa_id, judul, file_path)
          VALUES ('$mahasiswa_id', '$judul', '$filePaths')";

if (mysqli_query($koneksi, $query)) {
    header("Location: ../mahasiswa/upload-dokumen.php?success=1");
    exit;
} else {
    echo "Gagal menyimpan ke database: " . mysqli_error($koneksi);
}
?>
