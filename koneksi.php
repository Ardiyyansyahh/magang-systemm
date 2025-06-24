<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_magang";

$koneksi = new mysqli("localhost","root","Ardy24434","db_magang");
if($koneksi){
    echo '';
}else{
    echo 'Database Tidak Terkoneksi';
}
?>
