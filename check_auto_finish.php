<?php
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'no_user']); exit; }
$uid = intval($_SESSION['user_id']);
// find active break
$res = mysqli_query($conn, "SELECT * FROM istirahat WHERE user_id=$uid AND status='sedang' ORDER BY id DESC LIMIT 1");
if (mysqli_num_rows($res)==0) { echo json_encode(['status'=>'no_active']); exit; }
$row = mysqli_fetch_assoc($res);

// Pastikan timezone sudah di-set
date_default_timezone_set('Asia/Jakarta');

// Hitung waktu dengan benar menggunakan strtotime
$start = strtotime($row['waktu_mulai']);
$dur = intval($row['durasi']);
$end = $start + ($dur * 60); // waktu berakhir dalam detik (waktu_mulai + durasi dalam menit * 60)
$now = time();

// PENTING: Hanya selesaikan dan kirim notifikasi jika waktu BENAR-BENAR habis
// Menggunakan >= untuk memastikan waktu sudah benar-benar melewati waktu berakhir
// Jangan kirim notifikasi sebelum waktu istirahat benar-benar habis
if ($now >= $end) {
    $istirahat_id = intval($row['id']);
    
    // Pastikan status masih 'sedang' sebelum diupdate (untuk menghindari duplikasi)
    $check_status = mysqli_query($conn, "SELECT status FROM istirahat WHERE id=$istirahat_id AND status='sedang'");
    if (mysqli_num_rows($check_status) == 0) {
        // Status sudah bukan 'sedang', tidak perlu proses lagi
        echo json_encode(['status'=>'already_finished']);
        exit;
    }
    
    // Cek apakah sudah pernah dikirim notifikasi untuk istirahat ini (untuk menghindari duplikasi)
    $cek_notif = mysqli_query($conn, 
        "SELECT id FROM notifikasi 
         WHERE id_karyawan=$uid 
         AND pesan LIKE '%selesai otomatis%'
         AND waktu >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
         ORDER BY id DESC LIMIT 1"
    );
    
    // Update status istirahat menjadi selesai (HANYA setelah waktu benar-benar habis)
    mysqli_query($conn, "UPDATE istirahat SET status='selesai', waktu_selesai=NOW(), otomatis_selesai=1 WHERE id=$istirahat_id");
    
    // Hanya kirim notifikasi jika belum pernah dikirim dalam 5 menit terakhir
    // Notifikasi hanya dikirim SETELAH waktu istirahat benar-benar habis
    if (mysqli_num_rows($cek_notif) == 0) {
        $pesan = 'Waktu istirahat Anda telah habis (selesai otomatis).';
        mysqli_query($conn, "INSERT INTO notifikasi (id_karyawan, pesan) VALUES ($uid, '".mysqli_real_escape_string($conn,$pesan)."')");
        
        // Kirim FCM notification
        require_once 'send_fcm_notification.php';
        sendFCMNotification(
            $uid,
            'Waktu Istirahat Habis',
            $pesan,
            ['type' => 'istirahat_selesai', 'istirahat_id' => $istirahat_id]
        );
    }
    
    echo json_encode(['status'=>'finished', 'id'=>$istirahat_id]);
    exit;
}
$remain = $end - $now;
echo json_encode(['status'=>'running','remaining_seconds'=>$remain]);
?>