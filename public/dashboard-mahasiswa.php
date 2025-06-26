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
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto space-y-8">

        <!-- Header -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h1 class="text-2xl font-bold mb-2">Dashboard Mahasiswa</h1>
            <p class="text-gray-700">Nama: <strong><?= $_SESSION['nama'] ?></strong></p>
            <p class="text-sm text-gray-500 mb-4"><?= ucfirst($_SESSION['role']) ?></p>
            <p class="text-sm text-gray-500 mb-4">Npm<?= ucfirst($_SESSION['nim']) ?></p>
            <p class="text-gray-600">Anda dapat mengelola pendaftaran magang, unggah dokumen, dan laporan mingguan di
                sini.</p>
        </div>

        <!-- Status Pendaftaran -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-3 flex items-center gap-2">
                <i data-feather="clipboard"></i> Status Pendaftaran Magang
            </h2>
            <?php if ($p = mysqli_fetch_assoc($pendaftaran)): ?>
                <p>Perusahaan: <strong><?= $p['perusahaan'] ?></strong></p>
                <p>Status: <strong class="text-green-600"><?= ucfirst($p['status']) ?></strong></p>
            <?php else: ?>
                <p class="text-red-600">Belum mendaftar magang.</p>
                <a href="../mahasiswa/pendaftaran-magang.php"
                    class="mt-2 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Daftar
                    Sekarang</a>
            <?php endif; ?>
        </div>



        <!-- Upload Dokumen -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i data-feather="upload"></i> Upload Dokumen Magang
            </h2>
            <form action="../mahasiswa/upload-dokumen.php" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Judul Dokumen</label>
                    <input type="text" name="judul" required class="w-full border px-3 py-2 rounded">
                </div>
                <div>
                    <label class="block mb-1 font-medium">File Proposal (PDF)</label>
                    <input type="file" name="proposal" accept="application/pdf" required class="w-full">
                </div>
                <div>
                    <label class="block mb-1 font-medium">File Surat Pengantar (PDF)</label>
                    <input type="file" name="surat" accept="application/pdf" required class="w-full">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
            </form>

            <!-- Dokumen List -->
            <?php while ($d = mysqli_fetch_assoc($dokumen)):
                $files = explode(',', $d['file_path']);
                $proposal = trim($files[0]);
                $surat = trim($files[1]);
                ?>
                <div class="mt-6 border-t pt-4">
                    <p>Judul: <strong><?= $d['judul'] ?></strong></p>
                    <p>Status Verifikasi: <strong><?= ucfirst($d['status_verifikasi']) ?></strong></p>
                    <p>Komentar Dosen: <?= $d['komentar_dosen'] ?: '<em>Belum ada komentar</em>' ?></p>
                    <a href="../uploads/<?= $proposal ?>" target="_blank" class="text-blue-600 underline">Lihat
                        Proposal</a><br>
                    <a href="../uploads/<?= $surat ?>" target="_blank" class="text-blue-600 underline">Lihat Surat</a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Laporan Mingguan -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i data-feather="file-text"></i> Laporan Mingguan
                </h2>
                <a href="../mahasiswa/laporan_mingguan.php"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Laporan</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Minggu Ke</th>
                            <th class="px-4 py-2 text-left">Isi Laporan</th>
                            <th class="px-4 py-2 text-left">Nilai</th>
                            <th class="px-4 py-2 text-left">Komentar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($l = mysqli_fetch_assoc($laporan)): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?= $l['minggu_ke'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($l['isi_laporan']) ?></td>
                                <td class="px-4 py-2"><?= $l['nilai'] ?: '-' ?></td>
                                <td class="px-4 py-2"><?= $l['komentar'] ?: '-' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>feather.replace();</script>
</body>

</html>