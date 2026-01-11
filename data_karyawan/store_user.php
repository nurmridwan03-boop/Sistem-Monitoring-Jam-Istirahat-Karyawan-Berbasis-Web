<?php
include 'db.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$username = $_POST['username'];
$password = $_POST['password'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$umur = $_POST['umur'];
$divisi = $_POST['divisi'];
$role = $_POST['role'];

mysqli_query($conn,
"INSERT INTO users (id, nama, username, password, jenis_kelamin, umur, divisi, role)
 VALUES ('$id', '$nama', '$username', '$password', '$jenis_kelamin', '$umur', '$divisi', '$role')");

header("Location: users.php");
?>
