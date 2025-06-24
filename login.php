<?php
session_start();
include 'koneksi.php';

$login_id = $_POST['login_id'];
$password = $_POST['password'];

// Validasi: tidak boleh kosong
if (empty($login_id) || empty($password)) {
    header("Location: public/login.html?error=kosong");
    exit;
}

// Siapkan statement aman (menghindari SQL Injection)
$stmt = $koneksi->prepare("SELECT * FROM users WHERE nim = ? OR email = ? OR nama = ? LIMIT 1");
$stmt->bind_param("sss", $login_id, $login_id, $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        switch ($user['role']) {
            case 'mahasiswa':
                header("Location: public/dashboard-mahasiswa.php");
                break;
            case 'dosen':
                header("Location: public/dashboard-dosen.php");
                break;
            case 'admin':
                header("Location: public/dashboard-admin.php");
                break;
        }
        exit;
    } else {
        header("Location: public/login.html?error=password");
        exit;
    }
} else {
    header("Location: public/login.html?error=notfound");
    exit;
}
