<?php
session_start();
include 'db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Ambil parameter bulan dan tahun (default: bulan ini)
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Validasi bulan dan tahun
if ($bulan < 1 || $bulan > 12) $bulan = date('m');
if ($tahun < 2020 || $tahun > 2100) $tahun = date('Y');

// Query untuk mendapatkan semua riwayat istirahat dengan data karyawan dari tabel riwayat_istirahat
$query = "SELECT i.*, u.nama, u.divisi 
          FROM riwayat_istirahat i 
          JOIN users u ON i.user_id = u.id 
          WHERE i.waktu_mulai IS NOT NULL 
          AND i.waktu_mulai != '0000-00-00 00:00:00' 
          AND i.waktu_mulai != '0000-00-00'
          AND MONTH(i.waktu_mulai) = $bulan 
          AND YEAR(i.waktu_mulai) = $tahun 
          ORDER BY i.waktu_mulai DESC";

$result = mysqli_query($conn, $query);

// Set header untuk download Excel
$nama_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$filename = "riwayat_istirahat_" . $nama_bulan[$bulan-1] . "_" . $tahun . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename);
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>";
echo "<th>Nama</th>";
echo "<th>Divisi</th>";
echo "<th>Waktu Mulai</th>";
echo "<th>Waktu Selesai</th>";
echo "<th>Durasi (Menit)</th>";
echo "<th>Catatan</th>";
echo "</tr>";

while ($row = mysqli_fetch_assoc($result)) {
    // Skip data yang tidak valid
    if (empty($row['waktu_mulai']) || 
        $row['waktu_mulai'] == '0000-00-00 00:00:00' || 
        $row['waktu_mulai'] == '0000-00-00' ||
        strtotime($row['waktu_mulai']) === false) {
        continue;
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
    echo "<td>" . htmlspecialchars($row['divisi'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['waktu_mulai']) . "</td>";
    echo "<td>" . ($row['waktu_selesai'] && $row['waktu_selesai'] != '0000-00-00 00:00:00' ? htmlspecialchars($row['waktu_selesai']) : '-') . "</td>";
    echo "<td>" . ($row['durasi'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['catatan'] ?? '-') . "</td>";
    echo "</tr>";
}

echo "</table>";
exit;
?>

