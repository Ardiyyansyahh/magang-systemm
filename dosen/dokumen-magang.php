<?php
session_start();
include '../koneksi.php';

// Pastikan user dosen sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    die("Akses ditolak. Silakan login sebagai dosen.");
}

// Ambil semua dokumen magang mahasiswa
$query = "SELECT d.id, u.nama AS mahasiswa, d.judul, d.file_path, d.uploaded_at
          FROM dokumen_magang d
          JOIN users u ON d.mahasiswa_id = u.id
          ORDER BY d.uploaded_at DESC";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lihat Dokumen Mahasiswa - Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-blue-700 mb-4">Dokumen Magang Mahasiswa</h1>

        <table class="w-full text-sm text-left border border-gray-300">
            <thead class="bg-blue-100">
                <tr>
                    <th class="p-2 border">No</th>
                    <th class="p-2 border">Mahasiswa</th>
                    <th class="p-2 border">Judul</th>
                    <th class="p-2 border">Dokumen</th>
                    <th class="p-2 border">Tanggal Upload</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
                    $files = explode(',', $row['file_path']);
                ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-2 border"><?php echo $no++; ?></td>
                    <td class="p-2 border"><?php echo htmlspecialchars($row['mahasiswa']); ?></td>
                    <td class="p-2 border"><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td class="p-2 border space-y-1">
                        <?php foreach ($files as $file): ?>
                            <a href="../uploads/<?php echo trim($file); ?>" class="text-blue-600 underline" target="_blank">
                                <?php echo basename($file); ?>
                            </a><br>
                        <?php endforeach; ?>
                    </td>
                    <td class="p-2 border"><?php echo date('d-m-Y H:i', strtotime($row['uploaded_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
