<?php
include 'koneksi.php'; // file koneksi database

// Ambil data dari form
$nama      = $_POST['nama'];
$email     = $_POST['email'];
$password  = $_POST['password'];
$role      = 'mahasiswa'; // bisa dinamis tergantung form

// Data khusus mahasiswa
$nim        = $_POST['nim'];
$angkatan   = $_POST['angkatan'];
$perusahaan = $_POST['perusahaan'];

// Query simpan ke database
$query = "INSERT INTO users (role, nim, angkatan, nama, email, password)
          VALUES ('$role', '$nim', '$angkatan', '$nama', '$email', '$password')";

if (mysqli_query($koneksi, $query)) {
    header("Location: public/success.html");
    exit;
} else {
    echo "Gagal menyimpan data: " . mysqli_error($koneksi);
}
?>
