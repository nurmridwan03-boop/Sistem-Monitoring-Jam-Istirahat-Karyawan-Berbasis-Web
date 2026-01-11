<?php
/**
 * Script untuk memperbaiki tabel istirahat yang corrupt
 * Jalankan di browser: http://localhost/kkpv5/fix_istirahat_table.php
 */

include 'db.php';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Fix Table istirahat</title>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;} th{background:#f4f4f4;}</style></head><body>";
echo "<h2>Perbaikan Tabel istirahat</h2>";

// Langkah 1: Coba REPAIR TABLE
echo "<h3>Langkah 1: Mencoba REPAIR TABLE...</h3>";
$repair = mysqli_query($conn, "REPAIR TABLE istirahat");
if ($repair) {
    $row = mysqli_fetch_assoc($repair);
    if ($row['Msg_type'] == 'status' && strpos($row['Msg_text'], 'OK') !== false) {
        echo "<p class='success'>✓ Tabel berhasil diperbaiki!</p>";
        echo "<p><a href='dashboard.php'>Kembali ke Dashboard</a></p>";
        exit;
    } else {
        echo "<p class='warning'>⚠ " . htmlspecialchars($row['Msg_text']) . "</p>";
        echo "<p>REPAIR tidak berhasil, akan mencoba membuat ulang tabel...</p>";
    }
} else {
    echo "<p class='error'>✗ Error REPAIR: " . mysqli_error($conn) . "</p>";
    echo "<p>Will try to recreate table...</p>";
}

// Langkah 2: Jika REPAIR gagal, buat ulang tabel
if (!isset($_GET['recreate'])) {
    echo "<hr>";
    echo "<h3>Langkah 2: Membuat Ulang Tabel</h3>";
    echo "<p class='warning'><strong>PERINGATAN:</strong> Ini akan menghapus semua data di tabel istirahat!</p>";
    echo "<p><a href='?recreate=yes' style='background:red;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Klik untuk Membuat Ulang Tabel</a></p>";
    exit;
}

echo "<hr><h3>Langkah 2: Membuat Ulang Tabel istirahat...</h3>";

// Hapus tabel lama
mysqli_query($conn, "DROP TABLE IF EXISTS istirahat");
echo "<p class='success'>✓ Tabel lama dihapus</p>";

// Buat tabel baru
$create_sql = "CREATE TABLE `istirahat` (
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

if (mysqli_query($conn, $create_sql)) {
    echo "<p class='success'>✓ Tabel istirahat berhasil dibuat ulang!</p>";
    
    // Verifikasi
    echo "<h3>Verifikasi Struktur Tabel:</h3>";
    $check = mysqli_query($conn, "DESCRIBE istirahat");
    if ($check) {
        echo "<table>";
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
    echo "<p class='success'><strong>✓ SELESAI! Tabel istirahat berhasil dibuat ulang.</strong></p>";
    echo "<p><a href='dashboard.php' style='background:green;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Kembali ke Dashboard</a></p>";
} else {
    echo "<p class='error'>✗ Error membuat tabel: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
echo "</body></html>";
?>





