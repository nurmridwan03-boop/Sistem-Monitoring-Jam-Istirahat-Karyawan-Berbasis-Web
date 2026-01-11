<?php
/**
 * Script untuk membuat ulang tabel istirahat jika repair tidak berhasil
 * PERHATIKAN: Script ini akan menghapus tabel dan membuat ulang!
 * Pastikan sudah backup data jika ada data penting
 */

include 'db.php';

echo "<h2>Membuat Ulang Tabel istirahat</h2>";
echo "<p style='color: red;'><strong>PERINGATAN:</strong> Script ini akan menghapus tabel istirahat dan membuat ulang!</p>";

// Tanya konfirmasi
if (!isset($_GET['confirm']) || $_GET['confirm'] != 'yes') {
    echo "<p><a href='?confirm=yes' style='background: red; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Klik untuk Konfirmasi dan Lanjutkan</a></p>";
    echo "<p><a href='repair_table_istirahat.php'>Kembali ke Repair Table</a></p>";
    exit;
}

echo "<h3>1. Menghapus tabel istirahat (jika ada)...</h3>";
mysqli_query($conn, "DROP TABLE IF EXISTS istirahat");
echo "<p style='color: green;'>✓ Tabel lama dihapus</p>";

echo "<h3>2. Membuat tabel istirahat baru...</h3>";

// Buat tabel istirahat berdasarkan struktur yang digunakan di aplikasi
$create_table = "CREATE TABLE IF NOT EXISTS `istirahat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `durasi` int(11) DEFAULT NULL COMMENT 'Durasi dalam menit',
  `status` varchar(20) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `otomatis_selesai` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (mysqli_query($conn, $create_table)) {
    echo "<p style='color: green;'><strong>✓ Tabel istirahat berhasil dibuat ulang!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ Error:</strong> " . mysqli_error($conn) . "</p>";
    exit;
}

echo "<h3>3. Verifikasi struktur tabel...</h3>";
$check = mysqli_query($conn, "DESCRIBE istirahat");
if ($check) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($check)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p style='color: green;'><strong>✓ Selesai! Tabel istirahat berhasil dibuat ulang.</strong></p>";
echo "<p><a href='dashboard.php'>Kembali ke Dashboard</a></p>";

mysqli_close($conn);
?>





