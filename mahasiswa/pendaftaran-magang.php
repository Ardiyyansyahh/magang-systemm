<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.html");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahasiswa_id = $_SESSION['user_id'];
    $perusahaan_id = (int) $_POST['perusahaan_id'];
    $posisi = mysqli_real_escape_string($koneksi, $_POST['posisi']);

    // Cek apakah sudah mendaftar
    $cek = mysqli_query($koneksi, "SELECT * FROM pendaftaran_magang WHERE mahasiswa_id = $mahasiswa_id");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Anda sudah mendaftar magang.";
    } else {
        if (!isset($_FILES['surat']) || $_FILES['surat']['error'] !== 0) {
            $error = "File surat lamaran belum diunggah.";
        } else {
            $surat = $_FILES['surat'];
            $allowedTypes = ['application/pdf'];

            if (!in_array($surat['type'], $allowedTypes)) {
                $error = "Hanya file PDF yang diperbolehkan.";
            } else {
                $uploadDir = '../uploads/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $suratName = uniqid('lamaran_') . '.pdf';
                $suratPath = $uploadDir . $suratName;

                if (!move_uploaded_file($surat['tmp_name'], $suratPath)) {
                    $error = "Gagal upload file surat lamaran.";
                } else {
                    $query = "INSERT INTO pendaftaran_magang 
                        (mahasiswa_id, posisi, dokumen, status, tanggal_pengajuan, perusahaan_id)
                        VALUES ($mahasiswa_id, '$posisi', '$suratName', 'Menunggu', CURDATE(), $perusahaan_id)";

                    if (mysqli_query($koneksi, $query)) {
                        header("Location: ../public/dashboard-mahasiswa.php?success=1");
                        exit;
                    } else {
                        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
                    }
                }
            }
        }
    }
}

// Ambil data perusahaan mitra
$mitra = mysqli_query($koneksi, "SELECT id, nama FROM perusahaan_mitra ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Magang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Form Pendaftaran Magang</h1>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-medium mb-1">Pilih Perusahaan Mitra</label>
                <select name="perusahaan_id" required class="w-full border px-3 py-2 rounded">
                    <option value="">-- Pilih Perusahaan --</option>
                    <?php while ($p = mysqli_fetch_assoc($mitra)): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Posisi yang Dilamar</label>
                <input type="text" name="posisi" required class="w-full border px-3 py-2 rounded" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="surat">Unggah Surat Lamaran
                    (PDF)</label>
                <input type="file" id="surat" name="surat" accept="application/pdf"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                    required />
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim
                Pendaftaran</button>
        </form>
    </div>
</body>

</html>