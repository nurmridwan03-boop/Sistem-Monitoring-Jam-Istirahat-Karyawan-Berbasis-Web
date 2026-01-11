<?php
include 'db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_karyawan.csv');

$output = fopen("php://output", "w");

// Header kolom
fputcsv($output, ['ID', 'Nama', 'Jenis Kelamin', 'Umur', 'Divisi', 'Alamat', 'Email', 'Nomor HP', 'Username', 'Role']);

$res = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");

while ($row = mysqli_fetch_assoc($res)) {
    fputcsv($output, [
        $row['id'],
        $row['nama'],
        $row['jenis_kelamin'],
        $row['umur'],
        $row['divisi'],
        $row['alamat'],
        $row['email'],
        $row['nomerHP'] ?? '',
        $row['username'],
        $row['role']
    ]);
}

fclose($output);
exit;
?>
