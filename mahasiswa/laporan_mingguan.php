<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.html");
    exit;
}

$mahasiswa_id = $_SESSION['user_id'];
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $minggu_ke = (int) $_POST['minggu_ke'];
    $isi_laporan = mysqli_real_escape_string($koneksi, $_POST['isi_laporan']);

    $cek = mysqli_query($koneksi, "SELECT * FROM laporan_mingguan WHERE mahasiswa_id=$mahasiswa_id AND minggu_ke=$minggu_ke");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Laporan untuk minggu ke-$minggu_ke sudah ada.";
    } else {
        $query = "INSERT INTO laporan_mingguan (mahasiswa_id, minggu_ke, isi_laporan)
                  VALUES ($mahasiswa_id, $minggu_ke, '$isi_laporan')";
        if (mysqli_query($koneksi, $query)) {
            $success = "Laporan berhasil disimpan.";
        } else {
            $error = "Gagal menyimpan laporan: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Laporan Mingguan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-xl font-bold mb-4">Input Laporan Mingguan</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1">Minggu Ke-</label>
            <input type="number" name="minggu_ke" required class="w-full border px-3 py-2 rounded" />
        </div>
        <div>
            <label class="block mb-1">Isi Laporan</label>
            <textarea name="isi_laporan" required class="w-full border px-3 py-2 rounded"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim</button>
        <a href="../public/dashboard-mahasiswa.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kembali</a>
    </form>
</div>
</body>
</html>
