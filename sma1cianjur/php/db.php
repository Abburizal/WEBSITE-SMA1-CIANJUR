<?php
$host = "localhost"; // server
$user = "root";      // user default XAMPP
$pass = "";          // password default kosong
$db   = "sma1cianjur"; // nama database

// Koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($conn, "utf8");

echo "Koneksi berhasil!";
?>
