<?php
include 'db.php';

// Header Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_karyawan.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "
<tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Jenis Kelamin</th>
    <th>Umur</th>
    <th>Divisi</th>
    <th>Alamat</th>
    <th>Email</th>
    <th>Nomor HP</th>
    <th>Username</th>
    <th>Role</th>
</tr>
";

$res = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");

while ($row = mysqli_fetch_assoc($res)) {
    echo "
    <tr>
        <td>{$row['id']}</td>
        <td>{$row['nama']}</td>
        <td>{$row['jenis_kelamin']}</td>
        <td>{$row['umur']}</td>
        <td>{$row['divisi']}</td>
        <td>{$row['alamat']}</td>
        <td>{$row['email']}</td>
        <td>" . ($row['nomerHP'] ?? '-') . "</td>
        <td>{$row['username']}</td>
        <td>{$row['role']}</td>
    </tr>
    ";
}

echo "</table>";
exit;
?>
