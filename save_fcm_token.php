<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Ambil data dari POST
$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? mysqli_real_escape_string($conn, $input['token']) : '';
$device_info = isset($input['device_info']) ? mysqli_real_escape_string($conn, $input['device_info']) : '';
$user_id = intval($_SESSION['user_id']);

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'Token is required']);
    exit;
}

// Pastikan tabel fcm_tokens sudah ada (jika belum, buat tabel)
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'fcm_tokens'");
if (mysqli_num_rows($check_table) == 0) {
    // Buat tabel jika belum ada
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
    mysqli_query($conn, $create_table);
}

// Cek apakah token sudah ada untuk user ini
$check_token = mysqli_query($conn, 
    "SELECT id FROM fcm_tokens WHERE user_id = $user_id AND token = '$token'"
);

if (mysqli_num_rows($check_token) > 0) {
    // Update timestamp jika token sudah ada
    mysqli_query($conn, 
        "UPDATE fcm_tokens 
         SET updated_at = NOW(), device_info = '$device_info' 
         WHERE user_id = $user_id AND token = '$token'"
    );
    echo json_encode(['status' => 'success', 'message' => 'Token updated']);
} else {
    // Insert token baru
    $insert_result = mysqli_query($conn, 
        "INSERT INTO fcm_tokens (user_id, token, device_info) 
         VALUES ($user_id, '$token', '$device_info')
         ON DUPLICATE KEY UPDATE updated_at = NOW(), device_info = '$device_info'"
    );
    
    if ($insert_result) {
        echo json_encode(['status' => 'success', 'message' => 'Token saved']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save token: ' . mysqli_error($conn)]);
    }
}
?>



