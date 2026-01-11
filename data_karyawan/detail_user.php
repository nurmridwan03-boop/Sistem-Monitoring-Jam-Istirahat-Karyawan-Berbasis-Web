<?php 
include '../db.php'; 
$id = intval($_GET['id'] ?? 0); 

$u_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$u = null;
if ($u_query) {
    $u = mysqli_fetch_assoc($u_query);
}

if (!$u) {
    die("User tidak ditemukan!");
}
?>
<!DOCTYPE html>
<html>
<body>

<h2>Detail User</h2>

ID: <?= $u['id'] ?><br>
Nama: <?= $u['nama'] ?><br>
Username: <?= $u['username'] ?><br>
Password: <?= $u['password'] ?><br>
JK: <?= $u['jenis_kelamin'] ?><br>
Umur: <?= $u['umur'] ?><br>
Divisi: <?= $u['divisi'] ?><br>
Role: <?= $u['role'] ?><br>
Alamat: <?= $u['alamat'] ?><br>
Email: <?= $u['email'] ?><br>
Nomor HP: <?= $u['nomerHP'] ?? '-' ?><br>

<br>
Foto:<br>
<img src="uploads/<?= $u['foto'] ?>" width="120" style="border-radius:8px; border:1px solid #ccc;">

</body>
</html>
