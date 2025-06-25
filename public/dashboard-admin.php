<!-- Dashboard Admin - SIMAGA -->
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$jumlah_mahasiswa = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'mahasiswa'"));
$jumlah_dosen = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'dosen'"));
$jumlah_magang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM pendaftaran_magang WHERE status = 'Disetujui'"));
$users = mysqli_query($koneksi, "SELECT id, nama, nim, role FROM users WHERE role != 'admin' ORDER BY role, nama");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIMAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="w-full max-w-6xl bg-white rounded-xl shadow-lg overflow-hidden mx-auto mt-6">
        <div class="bg-purple-600 text-white p-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <h1 class="text-xl font-bold">SIMAGA</h1>
                <span class="text-sm bg-purple-700 px-2 py-0.5 rounded">Admin</span>
            </div>
            <button id="logout-admin" class="bg-purple-700 hover:bg-purple-800 px-3 py-1 rounded text-sm">Logout</button>
        </div>

        <div class="p-6">
            <?php if (isset($_GET['edit']) && $_GET['edit'] === 'success'): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded border border-green-300">
                    Akun berhasil diperbarui.
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-purple-50 p-4 rounded-lg border">
                    <h3 class="text-purple-800 mb-2 font-medium">Total Mahasiswa</h3>
                    <p class="text-2xl font-bold text-purple-600"><?= $jumlah_mahasiswa ?></p>
                    <p class="text-xs text-gray-500">terdaftar</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border">
                    <h3 class="text-blue-800 mb-2 font-medium">Total Dosen</h3>
                    <p class="text-2xl font-bold text-blue-600"><?= $jumlah_dosen ?></p>
                    <p class="text-xs text-gray-500">pembimbing</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border">
                    <h3 class="text-green-800 mb-2 font-medium">Magang Aktif</h3>
                    <p class="text-2xl font-bold text-green-600"><?= $jumlah_magang ?></p>
                    <p class="text-xs text-gray-500">sedang berlangsung</p>
                </div>
                <div class="bg-amber-50 p-4 rounded-lg border">
                    <h3 class="text-amber-800 mb-2 font-medium">Perusahaan</h3>
                    <p class="text-2xl font-bold text-amber-600">42</p>
                    <p class="text-xs text-gray-500">mitra magang</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border rounded-lg">
                    <div class="border-b px-4 py-3 flex justify-between items-center">
                        <h3 class="font-medium">Manajemen Pengguna</h3>
                        <a href="../admin/tambah-akun.php" class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">+ Tambah</a>
                    </div>
                    <div class="p-4">
                        <div class="overflow-y-auto max-h-64">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php while ($u = mysqli_fetch_assoc($users)): ?>
                                        <tr>
                                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($u['nama']) ?></td>
                                            <td class="px-4 py-2 text-sm"><?= ucfirst($u['role']) ?></td>
                                            <td class="px-4 py-2 text-sm">
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                                            </td>
                                            <td class="px-4 py-2 text-sm space-x-2">
                                                <a href="../admin/edit-akun.php?id=<?= $u['id'] ?>" class="text-purple-600 hover:underline">Edit</a>
                                                <form action="../admin/hapus-akun.php" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                                    <input type="hidden" name="nim" value="<?= htmlspecialchars($u['nim']) ?>">
                                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg">
                    <div class="border-b px-4 py-3 flex justify-between items-center">
                        <h3 class="font-medium">Perusahaan Mitra</h3>
                        <a href="../admin/tambah-perusahaan.php" class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">+ Tambah</a>
                    </div>
                    <div class="p-4">
                        <input type="text" placeholder="Cari perusahaan..." class="w-full text-sm border rounded-lg px-3 py-2 mb-4 focus:outline-none focus:ring-1 focus:ring-purple-500">
                        <!-- Tambahkan perusahaan dinamis di sini jika tersedia -->
                    </div>
                </div>
            </div>

            <!-- Monitoring Aktivitas Magang -->
            <section>
                <?php
                $monitoring = mysqli_query($koneksi, "
                    SELECT u.nama, pm.status,
                           (SELECT COUNT(*) FROM laporan_mingguan WHERE mahasiswa_id = u.id) AS laporan,
                           (SELECT COUNT(*) FROM laporan_mingguan WHERE mahasiswa_id = u.id AND komentar != '') AS feedback
                    FROM pendaftaran_magang pm
                    JOIN users u ON u.id = pm.mahasiswa_id
                    WHERE pm.status = 'Disetujui'
                ");
                ?>
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Monitoring Aktivitas Magang</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Mahasiswa</th>
                                <th class="px-4 py-2">Status Magang</th>
                                <th class="px-4 py-2">Laporan Terkirim</th>
                                <th class="px-4 py-2">Feedback Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($m = mysqli_fetch_assoc($monitoring)): ?>
                                <tr>
                                    <td class="px-4 py-2"><?= htmlspecialchars($m['nama']) ?></td>
                                    <td class="px-4 py-2 text-green-600"><?= $m['status'] ?></td>
                                    <td class="px-4 py-2"><?= $m['laporan'] ?></td>
                                    <td class="px-4 py-2"><?= $m['feedback'] ?> komentar</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
