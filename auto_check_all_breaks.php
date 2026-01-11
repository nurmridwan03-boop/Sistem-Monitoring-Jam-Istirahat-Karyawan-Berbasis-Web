<?php
// Script untuk mengecek dan menyelesaikan semua istirahat yang sudah habis waktunya
// Script ini bisa dipanggil via cron job atau polling dari dashboard
include 'db.php';
header('Content-Type: application/json');

// Set timezone ke Asia/Jakarta untuk konsistensi
date_default_timezone_set('Asia/Jakarta');

// Cari semua istirahat yang masih aktif (status='sedang')
$res = mysqli_query($conn, "SELECT * FROM istirahat WHERE status='sedang' ORDER BY id ASC");

$processed = 0;
$finished = 0;

while ($row = mysqli_fetch_assoc($res)) {
    $processed++;
    
    // Pastikan waktu_mulai dalam format yang benar
    $start = strtotime($row['waktu_mulai']);
    $dur = intval($row['durasi']);
    
    // Hitung waktu akhir: waktu_mulai + durasi (dalam menit)
    $end = $start + ($dur * 60); // durasi dalam menit, dikonversi ke detik
    $now = time();
    
    // Jika waktu sudah habis (dengan toleransi 1 detik untuk menghindari masalah rounding)
    if ($now >= $end) {
        $istirahat_id = intval($row['id']);
        $user_id = intval($row['user_id']);
        
        // Update status menjadi selesai
        $update_result = mysqli_query($conn, 
            "UPDATE istirahat 
             SET status='selesai', waktu_selesai=NOW(), otomatis_selesai=1 
             WHERE id=$istirahat_id"
        );
        
        if ($update_result) {
            // Cek apakah notifikasi sudah pernah dibuat untuk istirahat ini (untuk menghindari duplikasi)
            $cek_notif = mysqli_query($conn, 
                "SELECT id FROM notifikasi 
                 WHERE id_karyawan=$user_id 
                 AND pesan LIKE '%selesai otomatis%'
                 AND waktu >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                 ORDER BY id DESC LIMIT 1"
            );
            
            // Jika belum ada notifikasi dalam 5 menit terakhir, buat notifikasi baru
            if (mysqli_num_rows($cek_notif) == 0) {
                $pesan = 'Waktu istirahat Anda telah habis (selesai otomatis).';
                mysqli_query($conn, 
                    "INSERT INTO notifikasi (id_karyawan, pesan) 
                     VALUES ($user_id, '".mysqli_real_escape_string($conn, $pesan)."')"
                );
                
                // Kirim FCM notification
                require_once 'send_fcm_notification.php';
                sendFCMNotification(
                    $user_id,
                    'Waktu Istirahat Habis',
                    $pesan,
                    ['type' => 'istirahat_selesai', 'istirahat_id' => $istirahat_id]
                );
            }
            
            $finished++;
        }
    }
}

echo json_encode([
    'status' => 'success',
    'processed' => $processed,
    'finished' => $finished,
    'message' => "Memproses $processed istirahat aktif, $finished istirahat selesai otomatis"
]);
?>

