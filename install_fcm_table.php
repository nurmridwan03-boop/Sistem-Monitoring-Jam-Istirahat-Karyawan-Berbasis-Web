<?php
/**
 * Script untuk membuat tabel fcm_tokens
 * Jalankan sekali untuk setup tabel
 */

include 'db.php';

$create_table = "CREATE TABLE IF NOT EXISTS fcm_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token TEXT NOT NULL,
    device_info VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_token (user_id, token(255)),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $create_table)) {
    echo "Tabel fcm_tokens berhasil dibuat!<br>";
    
    // Cek apakah ada foreign key constraint
    $check_fk = mysqli_query($conn, "SHOW CREATE TABLE fcm_tokens");
    if ($check_fk) {
        $row = mysqli_fetch_assoc($check_fk);
        if (strpos($row['Create Table'], 'FOREIGN KEY') === false) {
            // Tambahkan foreign key jika belum ada (opsional)
            echo "Note: Foreign key constraint tidak ditambahkan. Jika diperlukan, tambahkan manual.<br>";
        }
    }
    
    echo "<br><a href='index.php'>Kembali ke halaman utama</a>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>



