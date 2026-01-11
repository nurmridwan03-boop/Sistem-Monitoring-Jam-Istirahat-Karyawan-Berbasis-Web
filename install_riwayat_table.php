<?php
/**
 * Script untuk membuat tabel riwayat_istirahat
 * Jalankan file ini sekali saja untuk membuat tabel riwayat_istirahat di database
 */

include 'db.php';

// Query untuk membuat tabel riwayat_istirahat
$sql = "CREATE TABLE IF NOT EXISTS `riwayat_istirahat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `durasi` int(11) DEFAULT NULL COMMENT 'Durasi dalam menit',
  `status` varchar(20) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `otomatis_selesai` tinyint(1) DEFAULT 0,
  `waktu_reset` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu ketika status direset',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_waktu_reset` (`waktu_reset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (mysqli_query($conn, $sql)) {
    echo "✓ Tabel riwayat_istirahat berhasil dibuat!<br>";
    echo "Anda dapat menghapus file ini setelah tabel berhasil dibuat.";
} else {
    echo "✗ Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>






