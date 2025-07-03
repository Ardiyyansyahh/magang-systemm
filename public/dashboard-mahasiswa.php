<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.html");
    exit;
}

$id = $_SESSION['user_id'];
$mahasiswa_id = $_SESSION['user_id'];

$perusahaanList = mysqli_query($koneksi, "SELECT * FROM perusahaan_mitra");

// Ambil data pendaftaran magang mahasiswa ini
$pendaftaran = mysqli_query($koneksi, "SELECT * FROM pendaftaran_magang WHERE mahasiswa_id = $id");

// Ambil dokumen magang
$dokumen = mysqli_query($koneksi, "SELECT * FROM dokumen_magang WHERE mahasiswa_id = $id");

// Ambil laporan mingguan
$laporan = mysqli_query($koneksi, "SELECT * FROM laporan_mingguan WHERE mahasiswa_id = $id ORDER BY minggu_ke");

$pendaftaran = mysqli_query($koneksi, "
    SELECT pm.*, mitra.nama AS nama_perusahaan, mitra.alamat AS alamat_perusahaan
    FROM pendaftaran_magang pm
    JOIN perusahaan_mitra mitra ON pm.perusahaan_id = mitra.id
    WHERE pm.mahasiswa_id = $mahasiswa_id
    LIMIT 1
");

// Hitung statistik untuk dashboard
$total_laporan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM laporan_mingguan WHERE mahasiswa_id = $id"));
$total_dokumen = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dokumen_magang WHERE mahasiswa_id = $id"));
$dokumen_approved = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dokumen_magang WHERE mahasiswa_id = $id AND status_verifikasi = 'Disetujui'"));

// Hitung progress magang (berdasarkan tahapan)
$progress_percentage = 0;
$pendaftaran_status = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT status FROM pendaftaran_magang WHERE mahasiswa_id = $id"));

if ($pendaftaran_status) {
    $progress_percentage += 25; // Pendaftaran selesai
    if ($pendaftaran_status['status'] === 'Disetujui') {
        $progress_percentage += 25; // Pendaftaran disetujui
    }
}
if ($total_dokumen > 0) {
    $progress_percentage += 25; // Ada dokumen yang diupload
}
if ($total_laporan >= 4) {
    $progress_percentage += 25; // Minimal 4 laporan mingguan
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto space-y-8">

        <!-- Header -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold text-blue-700">Dashboard Mahasiswa</h1>
                <a href="../login.html" class="bg-blue-500 text-white px-4 py-2 rounded">Logout</a>
            </div>
            <p class="text-gray-700">Nama: <strong><?= $_SESSION['nama'] ?></strong></p>
            <p class="text-gray-700">NIM: <strong><?= $_SESSION['nim'] ?></strong></p>
            <p class="text-gray-700">Fakultas: <strong><?= $_SESSION['fakultas'] ?></strong></p>
            <p class="text-gray-700">Jurusan : <strong><?= $_SESSION['bidang_keahlian'] ?></strong></p>
            <?php
            // Ambil nama perusahaan dari hasil query pendaftaran
            $nama_perusahaan = '-';
            if (
                $p_temp = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT mitra.nama AS nama_perusahaan
                FROM pendaftaran_magang pm
                JOIN perusahaan_mitra mitra ON pm.perusahaan_id = mitra.id
                WHERE pm.mahasiswa_id = $mahasiswa_id
                LIMIT 1
            "))
            ) {
                $nama_perusahaan = $p_temp['nama_perusahaan'];
            }
            ?>
            <p class="text-gray-700">Tempat Magang: <strong><?= htmlspecialchars($nama_perusahaan) ?></strong></p>
            <p class="text-gray-600"> Selamat datang di dashboard magang.</p>

        </div>

        <!-- Status Pendaftaran -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-3 flex items-center gap-2">
                <i data-feather="clipboard"></i> Status Pendaftaran Magang
            </h2>
            <?php if ($p = mysqli_fetch_assoc($pendaftaran)): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-gray-700">Perusahaan: <strong><?= htmlspecialchars($p['nama_perusahaan']) ?></strong></p>
                    <p class="text-gray-700">Status:
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?= $p['status'] === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                ($p['status'] === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </p>
                    <p class="text-gray-700">Posisi: <strong><?= htmlspecialchars($p['posisi']) ?></strong></p>
                    <p class="text-gray-600 text-sm">Tanggal Pengajuan:
                        <?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-600 mb-3">Belum mendaftar magang.</p>
                    <a href="../mahasiswa/pendaftaran-magang.php"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 hover:bg-blue-700 hover:shadow-lg">
                        <i data-feather="plus" class="w-4 h-4"></i>
                        Daftar Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>



        <!-- Upload Dokumen Magang -->
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

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
            </form>

            <!-- Dokumen List -->
            <?php
            $dokumen_fresh = mysqli_query($koneksi, "SELECT * FROM dokumen_magang WHERE mahasiswa_id = $id");
            if (mysqli_num_rows($dokumen_fresh) > 0):
                ?>
                <div class="mt-6 border-t pt-4">
                    <h3 class="font-semibold mb-3">Dokumen yang Telah Diupload:</h3>
                    <?php while ($d = mysqli_fetch_assoc($dokumen_fresh)):
                        $files = explode(',', $d['file_path']);
                        $proposal = trim($files[0]);
                        $surat = isset($files[1]) ? trim($files[1]) : '';
                        ?>
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($d['judul']) ?></p>
                                    <p class="text-sm text-gray-600">Upload:
                                        <?= date('d/m/Y H:i', strtotime($d['uploaded_at'])) ?>
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?= $d['status_verifikasi'] === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                        ($d['status_verifikasi'] === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                    <?= ucfirst($d['status_verifikasi']) ?>
                                </span>
                            </div>

                            <div class="flex gap-4 mb-2">
                                <a href="../uploads/<?= $proposal ?>" target="_blank"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm">
                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                    Lihat Proposal
                                </a>
                                <?php if ($surat): ?>
                                    <a href="../uploads/<?= $surat ?>" target="_blank"
                                        class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm">
                                        <i data-feather="mail" class="w-4 h-4"></i>
                                        Lihat Surat
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($d['komentar_dosen']): ?>
                                <div class="bg-blue-50 rounded p-3 mt-2">
                                    <p class="text-sm font-medium text-blue-800">Komentar Dosen:</p>
                                    <p class="text-sm text-blue-700"><?= htmlspecialchars($d['komentar_dosen']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="mt-6 bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                    <p>Belum ada dokumen yang diupload</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Laporan Mingguan -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i data-feather="file-text"></i> Laporan Mingguan
                </h2>
                <div class="flex gap-2">
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        Total: <?= $total_laporan ?> laporan
                    </span>
                    <a href="../mahasiswa/laporan_mingguan.php"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-feather="plus" class="w-4 h-4"></i>
                        Tambah Laporan
                    </a>
                </div>
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
                        <?php
                        $laporan_fresh = mysqli_query($koneksi, "SELECT * FROM laporan_mingguan WHERE mahasiswa_id = $id ORDER BY minggu_ke");
                        if (mysqli_num_rows($laporan_fresh) > 0):
                            while ($l = mysqli_fetch_assoc($laporan_fresh)):
                                ?>
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Minggu <?= $l['minggu_ke'] ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs">
                                            <p class="text-sm text-gray-900 line-clamp-2">
                                                <?= htmlspecialchars(substr($l['isi_laporan'], 0, 100)) ?>
                                                <?= strlen($l['isi_laporan']) > 100 ? '...' : '' ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if ($l['nilai']): ?>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?= $l['nilai'] >= 80 ? 'bg-green-100 text-green-800' :
                                                ($l['nilai'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= $l['nilai'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">Belum dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if ($l['komentar']): ?>
                                            <div class="max-w-xs">
                                                <p class="text-sm text-gray-600">
                                                    <?= htmlspecialchars(substr($l['komentar'], 0, 50)) ?>
                                                    <?= strlen($l['komentar']) > 50 ? '...' : '' ?>
                                                </p>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i data-feather="file-text" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                                    <p>Belum ada laporan mingguan</p>
                                    <a href="../mahasiswa/laporan_mingguan.php"
                                        class="text-blue-600 hover:text-blue-800 text-sm">Buat laporan pertama</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i data-feather="clock"></i> Aktivitas Terbaru
            </h2>
            <div class="space-y-4">
                <?php
                // Query untuk aktivitas terbaru
                $activities = [];

                // Ambil laporan terbaru
                $recent_reports = mysqli_query($koneksi, "SELECT minggu_ke, created_at FROM laporan_mingguan WHERE mahasiswa_id = $id ORDER BY created_at DESC LIMIT 3");
                while ($report = mysqli_fetch_assoc($recent_reports)) {
                    $activities[] = [
                        'type' => 'report',
                        'title' => 'Laporan Minggu ' . $report['minggu_ke'] . ' dibuat',
                        'time' => $report['created_at'],
                        'icon' => 'file-text',
                        'color' => 'blue'
                    ];
                }

                // Ambil dokumen terbaru
                $recent_docs = mysqli_query($koneksi, "SELECT judul, uploaded_at FROM dokumen_magang WHERE mahasiswa_id = $id ORDER BY uploaded_at DESC LIMIT 2");
                while ($doc = mysqli_fetch_assoc($recent_docs)) {
                    $activities[] = [
                        'type' => 'document',
                        'title' => 'Dokumen "' . $doc['judul'] . '" diupload',
                        'time' => $doc['uploaded_at'],
                        'icon' => 'upload',
                        'color' => 'green'
                    ];
                }

                // Sort activities by time
                usort($activities, function ($a, $b) {
                    return strtotime($b['time']) - strtotime($a['time']);
                });

                // Tampilkan 5 aktivitas terbaru
                $activities = array_slice($activities, 0, 5);

                if (empty($activities)): ?>
                    <div class="text-center text-gray-500 py-8">
                        <i data-feather="calendar" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                        <p>Belum ada aktivitas</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($activities as $activity): ?>
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-<?= $activity['color'] ?>-100 rounded-full flex items-center justify-center">
                                <i data-feather="<?= $activity['icon'] ?>"
                                    class="w-4 h-4 text-<?= $activity['color'] ?>-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($activity['title']) ?></p>
                                <p class="text-xs text-gray-500">
                                    <?php
                                    $time_diff = time() - strtotime($activity['time']);
                                    if ($time_diff < 3600) {
                                        echo floor($time_diff / 60) . ' menit yang lalu';
                                    } elseif ($time_diff < 86400) {
                                        echo floor($time_diff / 3600) . ' jam yang lalu';
                                    } else {
                                        echo floor($time_diff / 86400) . ' hari yang lalu';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        feather.replace();

        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function () {
            // Animate progress bar on load
            const progressBar = document.querySelector('.bg-gradient-to-r.from-blue-500.to-purple-600');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = '<?= $progress_percentage ?>%';
                }, 500);
            }
        });
    </script>
</body>

</html>