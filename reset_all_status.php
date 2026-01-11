<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Pastikan hanya admin yang bisa reset
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

// Reset semua status karyawan
// Ambil semua user yang memiliki record istirahat
$users = mysqli_query($conn, "SELECT DISTINCT user_id FROM istirahat");

$reset_count = 0;
$riwayat_count = 0;
while ($row = mysqli_fetch_assoc($users)) {
    $user_id = intval($row['user_id']);
    
    // Cari record istirahat terbaru untuk user tersebut dengan data lengkap
    $result = mysqli_query($conn, 
        "SELECT id, user_id, waktu_mulai, waktu_selesai, durasi, status, catatan, otomatis_selesai 
         FROM istirahat 
         WHERE user_id=$user_id 
         ORDER BY id DESC LIMIT 1"
    );
    
    if (mysqli_num_rows($result) > 0) {
        $istirahat_row = mysqli_fetch_assoc($result);
        $istirahat_id = intval($istirahat_row['id']);
        
        // Simpan data realtime ke riwayat_istirahat sebelum reset (hanya jika waktu_mulai valid)
        // Tidak simpan jika waktu_mulai NULL atau '0000-00-00 00:00:00'
        $waktu_mulai_trimmed = trim($istirahat_row['waktu_mulai'] ?? '');
        $waktu_mulai_valid = !empty($waktu_mulai_trimmed) 
                             && $waktu_mulai_trimmed != '0000-00-00 00:00:00' 
                             && $waktu_mulai_trimmed != '0000-00-00' 
                             && strpos($waktu_mulai_trimmed, '0000-00-00') === false
                             && strtotime($waktu_mulai_trimmed) !== false
                             && strtotime($waktu_mulai_trimmed) > 0;
        
        if ($waktu_mulai_valid) {
            $waktu_mulai = "'".mysqli_real_escape_string($conn, $waktu_mulai_trimmed)."'";
            $waktu_selesai = !empty($istirahat_row['waktu_selesai']) && $istirahat_row['waktu_selesai'] != '0000-00-00 00:00:00' && $istirahat_row['waktu_selesai'] != '0000-00-00' 
                             ? "'".mysqli_real_escape_string($conn, $istirahat_row['waktu_selesai'])."'" : "NULL";
            $durasi = !empty($istirahat_row['durasi']) ? intval($istirahat_row['durasi']) : "NULL";
            $status = !empty($istirahat_row['status']) ? "'".mysqli_real_escape_string($conn, $istirahat_row['status'])."'" : "NULL";
            $catatan = !empty($istirahat_row['catatan']) ? "'".mysqli_real_escape_string($conn, $istirahat_row['catatan'])."'" : "NULL";
            $otomatis_selesai = isset($istirahat_row['otomatis_selesai']) ? intval($istirahat_row['otomatis_selesai']) : 0;
            
            mysqli_query($conn,
                "INSERT INTO riwayat_istirahat (user_id, waktu_mulai, waktu_selesai, durasi, status, catatan, otomatis_selesai, waktu_reset)
                 VALUES ($user_id, $waktu_mulai, $waktu_selesai, $durasi, $status, $catatan, $otomatis_selesai, NOW())"
            );
            $riwayat_count++;
        }
        
        // Reset status, waktu_mulai, waktu_selesai, dan durasi
        mysqli_query($conn,
            "UPDATE istirahat 
             SET status=NULL, waktu_mulai=NULL, waktu_selesai=NULL, durasi=NULL 
             WHERE id=$istirahat_id"
        );
        $reset_count++;
    }
}

echo json_encode([
    'status' => 'success', 
    'msg' => "Berhasil mereset status $reset_count karyawan. $riwayat_count riwayat disimpan."
]);
?>

