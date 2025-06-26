<?php
session_start();
include '../koneksi.php';

// Cek apakah dosen sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../login.html");
    exit;
}

// Ambil data pendaftaran magang mahasiswa
$pendaftaranQuery = "SELECT pm.*, u.nama FROM pendaftaran_magang pm JOIN users u ON pm.mahasiswa_id = u.id";

$pendaftaranResult = mysqli_query($koneksi, $pendaftaranQuery);

// Ambil laporan mingguan
$laporanQuery = "SELECT lm.*, u.nama FROM laporan_mingguan lm JOIN users u ON lm.mahasiswa_id = u.id ORDER BY lm.minggu_ke ASC";
$laporanResult = mysqli_query($koneksi, $laporanQuery);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen - SIMAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold text-green-700">Dashboard Dosen</h1>
            <a href=".../login.html" class="bg-red-500 text-white px-4 py-2 rounded">Logout</a>
        </div>

        <!-- Pendaftaran Magang -->
        <h2 class="text-xl font-semibold mb-2">Pendaftaran Magang</h2>
        <table class="w-full table-auto border mb-6">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Perusahaan</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = mysqli_fetch_assoc($pendaftaranResult)): ?>
                    <tr>
                        <td class="px-4 py-2"><?= htmlspecialchars($p['nama']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($p['perusahaan']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($p['status']) ?></td>
                        <td class="px-4 py-2">
                            <form method="POST" action="../dosen/update-status-pendaftaran.php" class="inline">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <select name="status" onchange="this.form.submit()" class="border px-2 py-1 rounded">
                                    <option value="Menunggu" <?= $p['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu
                                    </option>
                                    <option value="Disetujui" <?= $p['status'] === 'Disetujui' ? 'selected' : '' ?>>Disetujui
                                    </option>
                                    <option value="Ditolak" <?= $p['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak
                                    </option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Dokumen Magang Mahasiswa -->
        <h2 class="text-xl font-semibold mt-8 mb-2">Dokumen Magang Mahasiswa</h2>
        <table class="w-full table-auto border mb-6">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2">Nama Mahasiswa</th>
                    <th class="px-4 py-2">Judul</th>
                    <th class="px-4 py-2">File</th>
                    <th class="px-4 py-2">Komentar</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $dokumenQuery = "SELECT dm.*, u.nama FROM dokumen_magang dm JOIN users u ON dm.mahasiswa_id = u.id ORDER BY dm.uploaded_at DESC";
                $dokumenResult = mysqli_query($koneksi, $dokumenQuery);
                while ($d = mysqli_fetch_assoc($dokumenResult)): ?>
                    <tr>
                        <td class="px-4 py-2"><?= $d['nama'] ?></td>
                        <td class="px-4 py-2"><?= $d['judul'] ?></td>
                        <td class="px-4 py-2">
                            <?php
                            $files = explode(',', $d['file_path']);
                            $proposal = trim($files[0]);
                            $surat = trim($files[1]);
                            ?>
                            <a href="../uploads/<?= $proposal ?>" target="_blank"
                                class="text-blue-600 hover:underline">Lihat Proposal</a><br>
                            <a href="../uploads/<?= $surat ?>" target="_blank" class="text-blue-600 hover:underline">Lihat
                                Surat</a>
                        </td>
                        <td class="px-4 py-2"><?= $d['komentar_dosen'] ?: '-' ?></td>
                        <td class="px-4 py-2"><?= ucfirst($d['status_verifikasi']) ?></td>
                        <td class="px-4 py-2">
                            <form method="POST" action="../dosen/update-status-pendaftaran.php">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <input type="text" name="komentar" placeholder="Komentar..."
                                    class="border px-2 py-1 mb-1 rounded w-full">
                                <select name="status" class="border px-2 py-1 w-full mb-1">
                                    <option value="Menunggu" <?= $d['status_verifikasi'] == 'Menunggu' ? 'selected' : '' ?>>
                                        Menunggu</option>
                                    <option value="Disetujui" <?= $d['status_verifikasi'] == 'Disetujui' ? 'selected' : '' ?>>
                                        Disetujui</option>
                                    <option value="Ditolak" <?= $d['status_verifikasi'] == 'Ditolak' ? 'selected' : '' ?>>
                                        Ditolak</option>
                                </select>
                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


        <!-- Laporan Mingguan -->
        <h2 class="text-xl font-semibold mb-2">Laporan Mingguan</h2>
        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2">Minggu</th>
                    <th class="px-4 py-2">Mahasiswa</th>
                    <th class="px-4 py-2">Isi Laporan</th>
                    <th class="px-4 py-2">Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($l = mysqli_fetch_assoc($laporanResult)): ?>
                    <tr>
                        <td class="px-4 py-2">Minggu <?= $l['minggu_ke'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($l['nama']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($l['isi_laporan']) ?></td>
                        <td class="px-4 py-2">
                            <form method="POST" action="../dosen/lihat-laporan.php" class="flex items-center">
                                <input type="hidden" name="id_laporan" value="<?= $l['id'] ?>">
                                <div class=" right flex">
                                    <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Perbaiki</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>