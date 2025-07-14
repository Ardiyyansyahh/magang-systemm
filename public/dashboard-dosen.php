<?php
// Include session helper
require_once '../includes/session_helper.php';

// Initialize session dengan konfigurasi yang lebih baik
initializeSession();

// Validate session dengan role checking
if (!validateUserSession('dosen')) {
    logActivity('Session validation failed', 'Redirecting to login');
    redirectToLogin('Session expired atau tidak valid');
}

include '../koneksi.php';

$pendaftaranQuery = "
    SELECT pm.id, pm.status, pm.posisi, pm.tanggal_pengajuan, u.nama AS nama, mitra.nama AS perusahaan
    FROM pendaftaran_magang pm
    JOIN users u ON pm.mahasiswa_id = u.id
    JOIN perusahaan_mitra mitra ON pm.perusahaan_id = mitra.id
    ORDER BY pm.tanggal_pengajuan DESC
";
$pendaftaranResult = mysqli_query($koneksi, $pendaftaranQuery);

// Ambil laporan mingguan
$laporanQuery = "SELECT lm.*, u.nama FROM laporan_mingguan lm JOIN users u ON lm.mahasiswa_id = u.id ";
$laporanResult = mysqli_query($koneksi, $laporanQuery);

// Hitung statistik untuk dashboard dengan error handling
$total_mahasiswa = 0;
$pending_approval = 0;
$pending_documents = 0;
$total_laporan = 0;
$rata_nilai = 0;

try {
    // Total mahasiswa yang mendaftar magang
    $result = mysqli_query($koneksi, "SELECT DISTINCT mahasiswa_id FROM pendaftaran_magang");
    $total_mahasiswa = $result ? mysqli_num_rows($result) : 0;
    
    // Pending approval
    $result = mysqli_query($koneksi, "SELECT * FROM pendaftaran_magang WHERE status = 'Menunggu'");
    $pending_approval = $result ? mysqli_num_rows($result) : 0;
    
    // Pending documents
    $result = mysqli_query($koneksi, "SELECT * FROM dokumen_magang WHERE status_verifikasi = 'Menunggu'");
    $pending_documents = $result ? mysqli_num_rows($result) : 0;
    
    // Total laporan
    $result = mysqli_query($koneksi, "SELECT * FROM laporan_mingguan");
    $total_laporan = $result ? mysqli_num_rows($result) : 0;
    
    // Hitung rata-rata nilai
    $avg_query = mysqli_query($koneksi, "SELECT AVG(nilai) as rata_nilai FROM laporan_mingguan WHERE nilai IS NOT NULL");
    if ($avg_query && $avg_result = mysqli_fetch_assoc($avg_query)) {
        $rata_nilai = $avg_result['rata_nilai'] ? round($avg_result['rata_nilai'], 1) : 0;
    }
} catch (Exception $e) {
    error_log("Database error in dashboard: " . $e->getMessage());
}


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen - SIMAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="bg-white p-6 rounded-xl shadow">
            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">
                        <?php 
                        switch($_GET['success']) {
                            case 'nilai_tersimpan': echo 'Nilai berhasil disimpan!'; break;
                            default: echo 'Operasi berhasil dilakukan!';
                        }
                        ?>
                    </span>
                    <button onclick="this.parentElement.style.display='none'" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">
                        <?php 
                        switch($_GET['error']) {
                            case 'nilai_invalid': echo 'Nilai harus antara 0-100!'; break;
                            case 'gagal_simpan': echo 'Gagal menyimpan data!'; break;
                            default: echo 'Terjadi kesalahan!';
                        }
                        ?>
                    </span>
                    <button onclick="this.parentElement.style.display='none'" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-700">Dashboard Dosen</h1>
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                            <i data-feather="bell" class="w-6 h-6"></i>
                            <?php if ($pending_approval + $pending_documents > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    <?= $pending_approval + $pending_documents ?>
                                </span>
                            <?php endif; ?>
                        </button>
                    </div>
                    <a href="../login.html" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Logout</a>
                </div>
            </div>

        <!-- Pendaftaran Magang -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i data-feather="clipboard"></i> Pendaftaran Magang
                </h2>
                <div class="flex gap-4">
                    <div class="relative">
                        <input type="text" id="searchPendaftaran" placeholder="Cari nama mahasiswa..." 
                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i data-feather="search" class="w-4 h-4 absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select id="filterStatus" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto border" id="tablePendaftaran">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="px-4 py-3 cursor-pointer hover:bg-gray-300" onclick="sortTable(0)">
                                <div class="flex items-center gap-2">
                                    Nama <i data-feather="chevrons-up-down" class="w-4 h-4"></i>
                                </div>
                            </th>
                            <th class="px-4 py-3">Perusahaan</th>
                            <th class="px-4 py-3">Posisi</th>
                            <th class="px-4 py-3 cursor-pointer hover:bg-gray-300" onclick="sortTable(3)">
                                <div class="flex items-center gap-2">
                                    Tanggal <i data-feather="chevrons-up-down" class="w-4 h-4"></i>
                                </div>
                            </th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pendaftaranResult = mysqli_query($koneksi, $pendaftaranQuery);
                        while ($p = mysqli_fetch_assoc($pendaftaranResult)): 
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($p['nama']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($p['perusahaan']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($p['posisi']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $p['status'] === 'Disetujui' ? 'bg-green-100 text-green-800' : 
                                            ($p['status'] === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        <?= htmlspecialchars($p['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="../dosen/update-status-pendaftaran.php" class="inline">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" 
                                                class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-blue-500">
                                            <option value="Menunggu" <?= $p['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                            <option value="Disetujui" <?= $p['status'] === 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                            <option value="Ditolak" <?= $p['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dokumen Magang Mahasiswa -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i data-feather="folder"></i> Dokumen Magang Mahasiswa
                </h2>
                <div class="flex gap-4">
                    <div class="relative">
                        <input type="text" id="searchDokumen" placeholder="Cari nama atau judul..." 
                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i data-feather="search" class="w-4 h-4 absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select id="filterDokumen" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto border" id="tableDokumen">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="px-4 py-3">Nama Mahasiswa</th>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">File</th>
                            <th class="px-4 py-3">Upload</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dokumenQuery = "SELECT dm.*, u.nama FROM dokumen_magang dm JOIN users u ON dm.mahasiswa_id = u.id ORDER BY dm.uploaded_at DESC";
                        $dokumenResult = mysqli_query($koneksi, $dokumenQuery);
                        while ($d = mysqli_fetch_assoc($dokumenResult)): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($d['nama']) ?></td>
                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <p class="truncate"><?= htmlspecialchars($d['judul']) ?></p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    $files = explode(',', $d['file_path']);
                                    $proposal = trim($files[0]);
                                    ?>
                                    <a href="../uploads/<?= $proposal ?>" target="_blank"
                                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm">
                                        <i data-feather="external-link" class="w-4 h-4"></i>
                                        Lihat Proposal
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <?= date('d/m/Y H:i', strtotime($d['uploaded_at'])) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $d['status_verifikasi'] === 'Disetujui' ? 'bg-green-100 text-green-800' : 
                                            ($d['status_verifikasi'] === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        <?= ucfirst($d['status_verifikasi']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-2">
                                        <?php if ($d['komentar_dosen']): ?>
                                            <div class="text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                                <?= htmlspecialchars($d['komentar_dosen']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <form method="POST" action="../dosen/verifikasi-dokumen.php" class="space-y-2">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            <input type="text" name="komentar" placeholder="Tambah komentar..." 
                                                   value="<?= htmlspecialchars($d['komentar_dosen']) ?>"
                                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500">
                                            <div class="flex gap-2">
                                                <select name="status" class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500">
                                                    <option value="Menunggu" <?= $d['status_verifikasi'] == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                                    <option value="Disetujui" <?= $d['status_verifikasi'] == 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                                    <option value="Ditolak" <?= $d['status_verifikasi'] == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                                </select>
                                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Laporan Mingguan -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i data-feather="book-open"></i> Laporan Mingguan
                </h2>
                <div class="flex gap-4">
                    <div class="relative">
                        <input type="text" id="searchLaporan" placeholder="Cari nama mahasiswa..." 
                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i data-feather="search" class="w-4 h-4 absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select id="filterMinggu" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Minggu</option>
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>">Minggu <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto border" id="tableLaporan">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="px-4 py-3">Minggu</th>
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="px-4 py-3">Isi Laporan</th>
                            <th class="px-4 py-3">Nilai</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $laporanResult = mysqli_query($koneksi, $laporanQuery);
                        while ($l = mysqli_fetch_assoc($laporanResult)): 
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Minggu <?= $l['minggu_ke'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($l['nama']) ?></td>
                                <td class="px-4 py-3">
                                    <div class="max-w-md">
                                        <p class="text-sm text-gray-900 line-clamp-2">
                                            <?= htmlspecialchars(substr($l['isi_laporan'], 0, 100)) ?><?= strlen($l['isi_laporan']) > 100 ? '...' : '' ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($l['nilai']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?= $l['nilai'] >= 80 ? 'bg-green-100 text-green-800' : 
                                                ($l['nilai'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                            <?= $l['nilai'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">Belum dinilai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <?= isset($l['created_at']) ? date('d/m/Y', strtotime($l['created_at'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <form method="POST" action="../dosen/lihat-laporan.php" class="inline">
                                            <input type="hidden" name="id_laporan" value="<?= $l['id'] ?>">
                                            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition-colors">
                                                Review
                                            </button>
                                        </form>
                                        
                                        <?php if (!$l['nilai']): ?>
                                            <button onclick="openGradingModal(<?= $l['id'] ?>, '<?= htmlspecialchars($l['nama']) ?>', <?= $l['minggu_ke'] ?>)" 
                                                    class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition-colors">
                                                Nilai
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Grading Modal -->
    <div id="gradingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Beri Nilai Laporan</h3>
            <form method="POST" action="../dosen/simpan-nilai.php">
                <input type="hidden" id="laporanId" name="laporan_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa:</label>
                    <p id="mahasiswaNama" class="text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minggu:</label>
                    <p id="mingguKe" class="text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label for="nilai" class="block text-sm font-medium text-gray-700 mb-2">Nilai (0-100):</label>
                    <input type="number" id="nilai" name="nilai" min="0" max="100" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar:</label>
                    <textarea id="komentar" name="komentar" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeGradingModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        feather.replace();
        
        // Search functionality
        function setupSearch(searchId, tableId, columnIndex) {
            document.getElementById(searchId).addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const table = document.getElementById(tableId);
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                
                for (let row of rows) {
                    const cell = row.getElementsByTagName('td')[columnIndex];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        row.style.display = text.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
                    }
                }
            });
        }
        
        // Filter functionality
        function setupFilter(filterId, tableId, columnIndex) {
            document.getElementById(filterId).addEventListener('change', function() {
                const filter = this.value;
                const table = document.getElementById(tableId);
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                
                for (let row of rows) {
                    const cell = row.getElementsByTagName('td')[columnIndex];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        row.style.display = filter === '' || text.includes(filter) ? '' : 'none';
                    }
                }
            });
        }
        
        // Sort table functionality
        function sortTable(columnIndex) {
            const table = document.getElementById('tablePendaftaran');
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = Array.from(tbody.getElementsByTagName('tr'));
            
            rows.sort((a, b) => {
                const aText = a.getElementsByTagName('td')[columnIndex].textContent;
                const bText = b.getElementsByTagName('td')[columnIndex].textContent;
                return aText.localeCompare(bText);
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // Grading modal functions
        function openGradingModal(laporanId, mahasiswaNama, mingguKe) {
            document.getElementById('laporanId').value = laporanId;
            document.getElementById('mahasiswaNama').textContent = mahasiswaNama;
            document.getElementById('mingguKe').textContent = 'Minggu ' + mingguKe;
            document.getElementById('gradingModal').classList.remove('hidden');
        }
        
        function closeGradingModal() {
            document.getElementById('gradingModal').classList.add('hidden');
        }
        
        // Initialize search and filter
        setupSearch('searchPendaftaran', 'tablePendaftaran', 0);
        setupFilter('filterStatus', 'tablePendaftaran', 4);
        
        setupSearch('searchDokumen', 'tableDokumen', 0);
        setupFilter('filterDokumen', 'tableDokumen', 4);
        
        setupSearch('searchLaporan', 'tableLaporan', 1);
        setupFilter('filterMinggu', 'tableLaporan', 0);
    </script>