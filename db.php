<?php
// Set timezone untuk konsistensi (penting untuk InfinityFree hosting)
date_default_timezone_set('Asia/Jakarta');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "monitoring_istirahat";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone untuk MySQL juga
mysqli_query($conn, "SET time_zone = '+07:00'");
?>