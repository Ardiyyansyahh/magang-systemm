<?php
include '../koneksi.php';
$error = '';

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $nim = $_POST['nim'];
    $angkatan = $_POST['angkatan'];
    $fakultas = $_POST['fakultas'];
    $bidang_keahlian = $_POST['bidang_keahlian'];
    $role = $_POST['role'];

    if (!$nama || !$email || !$password || !$role) {
        $error = "Semua field wajib diisi.";
    } else {
        $sql = "INSERT INTO users (nama, email, password, role, nim, angkatan, fakultas, bidang_keahlian)
                VALUES ('$nama', '$email', '$password', '$role', '$nim', '$angkatan', '$fakultas', '$bidang_keahlian')";

        if (mysqli_query($koneksi, $sql)) {
            header("Location: ../public/dashboard-admin.php?success=akun-ditambahkan");
            exit;
        } else {
            $error = "Gagal menambahkan akun: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Akun Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <form method="POST" class="w-full max-w-3xl bg-white p-8 rounded-xl shadow-lg space-y-6">
        <h2 class="text-2xl font-bold text-purple-700 border-b pb-2 flex items-center gap-2">
            <i data-feather="user-plus"></i> Tambah Akun Pengguna
        </h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded border border-red-300">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $fields = [
                ['label' => 'Nama Lengkap', 'name' => 'nama', 'icon' => 'user', 'type' => 'text'],
                ['label' => 'Email', 'name' => 'email', 'icon' => 'mail', 'type' => 'email'],
                ['label' => 'Password', 'name' => 'password', 'icon' => 'lock', 'type' => 'text'],
                ['label' => 'NIM', 'name' => 'nim', 'icon' => 'hash', 'type' => 'text'],
                ['label' => 'Angkatan', 'name' => 'angkatan', 'icon' => 'calendar', 'type' => 'text'],
                ['label' => 'Fakultas', 'name' => 'fakultas', 'icon' => 'book', 'type' => 'text'],
                ['label' => 'Bidang Keahlian', 'name' => 'bidang_keahlian', 'icon' => 'briefcase', 'type' => 'text']
            ];

            foreach ($fields as $f): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $f['label'] ?></label>
                    <div class="flex items-center border rounded-lg px-3">
                        <i data-feather="<?= $f['icon'] ?>" class="text-gray-400"></i>
                        <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>" required
                            class="w-full px-2 py-2 focus:outline-none" />
                    </div>
                </div>
            <?php endforeach; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <div class="flex items-center border rounded-lg px-3">
                    <i data-feather="users" class="text-gray-400"></i>
                    <select name="role" required class="w-full px-2 py-2 focus:outline-none">
                        <option value="">-- Pilih Role --</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="text-right pt-4">
            <button type="submit"
                class="bg-purple-600 hover:bg-purple-700 transition-colors text-white font-semibold px-6 py-2 rounded-lg flex items-center gap-2">
                <i data-feather="plus-circle"></i> Tambah Akun
            </button>
        </div>
    </form>

    <script>
        feather.replace();
    </script>
</body>

</html>