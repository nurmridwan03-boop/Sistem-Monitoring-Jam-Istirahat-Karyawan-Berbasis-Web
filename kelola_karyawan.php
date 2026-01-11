<?php
include "config.php";

// cek admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$karyawan = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY id DESC");

// include sidebar
include "sidebar_admin.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="content">
    <h2>Data Karyawan</h2>

    <a href="tambah_karyawan.php" class="btn-start">+ Tambah Karyawan</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0" style="width:90%; background:white;">
        <tr style="background:#ddd; text-align:center;">
            <th width="50">ID</th>
            <th>Nama</th>
            <th>Username</th>
            <th width="100">Role</th>
            <th width="250">Aksi</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($karyawan)): ?>
        <tr>
            <td align="center"><?= $row['id'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['username'] ?></td>
            <td align="center"><?= ucfirst($row['role']) ?></td>
            <td align="center">
                <a href="detail_karyawan.php?id=<?= $row['id'] ?>" class="btn-start">Detail</a>
                <a href="edit_karyawan.php?id=<?= $row['id'] ?>" class="btn-start">Edit</a>
                <a href="hapus_karyawan.php?id=<?= $row['id'] ?>"
                   class="btn-end"
                   onclick="return confirm('Yakin ingin menghapus karyawan ini?');">
                   Hapus
                </a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>
</div>

</body>
</html>
