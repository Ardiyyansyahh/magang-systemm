<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.html");
    exit;
}

$id = $_SESSION['user_id'];

// Ambil data pendaftaran magang mahasiswa ini
$pendaftaran = mysqli_query($koneksi, "SELECT * FROM pendaftaran_magang WHERE mahasiswa_id = $id");

// Ambil dokumen magang
$dokumen = mysqli_query($koneksi, "SELECT * FROM dokumen_magang WHERE mahasiswa_id = $id");

// Ambil laporan mingguan
$laporan = mysqli_query($koneksi, "SELECT * FROM laporan_mingguan WHERE mahasiswa_id = $id ORDER BY minggu_ke");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">

        <h1 class="text-2xl font-bold mb-4">Dashboard Mahasiswa</h1>

        <p>Nama Mahasiswa <?= $_SESSION['nama'] ?></p>
        <p class="text-sm text-gray-600 mb-10">Role: <?= ucfirst($_SESSION['role']) ?></p>
        <p class="mb-10">Anda dapat mengelola pendaftaran magang, mengunggah dokumen, dan membuat laporan mingguan di sini.</p>

        <!-- STATUS PENDAFTARAN -->
        <h2 class="text-xl font-semibold mb-2">Status Pendaftaran Magang</h2>
        <?php if ($p = mysqli_fetch_assoc($pendaftaran)): ?>
        <p>Perusahaan: <strong>
                <?= $p['perusahaan'] ?>
            </strong></p>
        <p>Status: <strong class="text-green-600">
                <?= ucfirst($p['status']) ?>
            </strong></p>
        <?php else: ?>
        <p class="text-red-600">Belum mendaftar magang.</p>
         <a href="../mahasiswa/pendaftaran-magang.php" class="inline-block mt-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Daftar Sekarang</a>
        <?php endif; ?>



        <!-- DOKUMEN MAGANG -->
        <h2 class="text-xl font-semibold mt-6 mb-2">Dokumen Magang</h2>
        <form action="../mahasiswa/upload-dokumen.php" method="POST" enctype="multipart/form-data" class="mb-6 border p-4 rounded bg-gray-50">
    <h3 class="text-lg font-semibold mb-2">Upload Dokumen Baru</h3>
    <div class="mb-2">
        <label class="block mb-1 font-medium">Judul Dokumen</label>
        <input type="text" name="judul" required class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-2">
        <label class="block mb-1 font-medium">File Proposal (PDF)</label>
        <input type="file" name="proposal" accept="application/pdf" required class="w-full">
    </div>
    <div class="mb-2">
        <label class="block mb-1 font-medium">File Surat Pengantar (PDF)</label>
        <input type="file" name="surat" accept="application/pdf" required class="w-full">
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
</form>

        <?php while ($d = mysqli_fetch_assoc($dokumen)): 
        $files = explode(',', $d['file_path']);
        $proposal = trim($files[0]);
        $surat = trim($files[1]);
    ?>
        <div class="mb-4 border p-4 rounded">
            <p>Judul: <strong>
                    <?= $d['judul'] ?>
                </strong></p>
            <p>Status Verifikasi: <strong>
                    <?= ucfirst($d['status_verifikasi']) ?>
                </strong></p>
            <p>Komentar Dosen:
                <?= $d['komentar_dosen'] ?: '<em>Belum ada komentar</em>' ?>
            </p>
            <a href="../uploads/<?= $proposal ?>" target="_blank" class="text-blue-600 underline">Lihat Proposal</a><br>
            <a href="../uploads/<?= $surat ?>" target="_blank" class="text-blue-600 underline">Lihat Surat</a>
        </div>
        <?php endwhile; ?>

        <!-- LAPORAN MINGGUAN -->
        <h2 class="text-xl font-semibold mt-6 mb-2">Laporan Mingguan</h2>
        <a href="../mahasiswa/laporan_mingguan.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Laporan</a>

        <table class="w-full border table-auto mb-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2">Minggu Ke</th>
                    <th class="px-4 py-2">Isi Laporan</th>
                    <th class="px-4 py-2">Nilai</th>
                    <th class="px-4 py-2">Komentar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($l = mysqli_fetch_assoc($laporan)): ?>
                <tr>
                    <td class="px-4 py-2">
                        <?= $l['minggu_ke'] ?>
                    </td>
                    <td class="px-4 py-2">
                        <?= htmlspecialchars($l['isi_laporan']) ?>
                    </td>
                    <td class="px-4 py-2">
                        <?= $l['nilai'] ?: '-' ?>
                    </td>
                    <td class="px-4 py-2">
                        <?= $l['komentar'] ?: '-' ?>
                    </td>
                </tr>
                
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</body>

</html>