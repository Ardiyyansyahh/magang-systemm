<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    die("Akses ditolak. Silakan login sebagai dosen.");
}

$query = "SELECT l.*, u.nama 
          FROM laporan_mingguan l
          JOIN users u ON l.mahasiswa_id = u.id
          ORDER BY l.minggu_ke ASC";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mingguan Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Laporan Mingguan Mahasiswa</h1>

        <table class="w-full border table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-3 py-2 border">Minggu Ke</th>
                    <th class="px-3 py-2 border">Nama Mahasiswa</th>
                    <th class="px-3 py-2 border">Isi Laporan</th>
                    <th class="px-3 py-2 border">Nilai</th>
                    <th class="px-3 py-2 border">Komentar</th>
                    <th class="px-3 py-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($l = mysqli_fetch_assoc($result)): ?>
                <tr id="row-<?= $l['id'] ?>" class="border-t">
                    <td class="px-3 py-2 border">Minggu <?= $l['minggu_ke'] ?></td>
                    <td class="px-3 py-2 border"><?= htmlspecialchars($l['nama']) ?></td>
                    <td class="px-3 py-2 border"><?= nl2br(htmlspecialchars($l['isi_laporan'])) ?></td>
                    <td class="px-3 py-2 border"><?= $l['nilai'] ?? '-' ?></td>
                    <td class="px-3 py-2 border"><?= $l['komentar'] ?? '-' ?></td>
                    <td class="px-3 py-2 border">
                        <input type="number" id="nilai-<?= $l['id'] ?>" value="<?= $l['nilai'] ?? '' ?>" class="border px-1 w-16 mb-1">
                        <input type="text" id="komentar-<?= $l['id'] ?>" value="<?= $l['komentar'] ?? '' ?>" class="border px-2 w-48 mb-1">
                        <button onclick="simpan(<?= $l['id'] ?>)" class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">Simpan</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function simpan(id) {
            const nilai = $('#nilai-' + id).val();
            const komentar = $('#komentar-' + id).val();

            $.post('../dosen/simpan-nilai.php', {
                id_laporan: id,
                nilai: nilai,
                komentar: komentar
            }, function(res) {
                alert(res.message || 'Tersimpan!');
                location.reload();
            }, 'json');
        }
    </script>
</body>
</html>
