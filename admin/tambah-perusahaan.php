<?php
session_start();
include '../koneksi.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

// Tangani form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);

    if (!empty($nama)) {
        $insert = "INSERT INTO perusahaan_mitra (nama, alamat, kontak) VALUES ('$nama', '$alamat', '$kontak')";
        if (mysqli_query($koneksi, $insert)) {
            header("Location: ../public/dashboard-admin.php?perusahaan=success");
            exit;
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Nama perusahaan tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Perusahaan </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Tambah Perusahaan</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 text-red-600 bg-red-100 border border-red-300 p-2 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block mb-1 font-medium">Nama Perusahaan</label>
                <input type="text" name="nama" required class="w-full border px-3 py-2 rounded" />
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border px-3 py-2 rounded"></textarea>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Kontak (Email/No. Telp)</label>
                <input type="text" name="kontak" class="w-full border px-3 py-2 rounded" />
            </div>
            <div class="flex justify-between items-center">
                <a href="../public/dashboard-admin.php"
                    class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">
                    ← Kembali
                </a>

                <button type="submit" class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">
                    Simpan
                </button>
            </div>


        </form>
    </div>
</body>

</html>