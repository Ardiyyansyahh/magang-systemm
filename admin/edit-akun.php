<?php
session_start();
include '../koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = (int) $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = $id");
if (!$query || mysqli_num_rows($query) == 0) {
    die("Data tidak ditemukan.");
}

$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Edit Akun</h2>

        <form method="POST" action="proses-edit-akun.php">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" 
                       class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                       class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Angkatan</label>
                <input type="number" name="angkatan" value="<?= htmlspecialchars($user['angkatan']) ?>" 
                       class="w-full px-3 py-2 border rounded-lg" required>
            </div>


            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded-lg" required>
                    <option value="mahasiswa" <?= $user['role'] == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                    <option value="dosen" <?= $user['role'] == 'dosen' ? 'selected' : '' ?>>Dosen</option>
                </select>
            </div>

            <div class="mb-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</body>
</html>
