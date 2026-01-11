<?php
session_start();
include 'db.php';

// Pastikan timezone sudah di-set (penting untuk InfinityFree hosting)
date_default_timezone_set('Asia/Jakarta');

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Ambil ID user & role
$user_id = intval($_SESSION['user_id']);
$role    = $_SESSION['role'];

// LOGOUT
if (isset($_GET['logout'])) { 
    session_destroy(); 
    header('Location: index.php'); 
    exit; 
}

// MULAI ISTIRAHAT
if (isset($_POST['mulai'])) {
    // Untuk karyawan: cek apakah hari ini sudah ada istirahat
    if ($role == 'karyawan') {
        $check_query = mysqli_query($conn, 
            "SELECT id FROM istirahat 
             WHERE user_id=$user_id 
             AND DATE(waktu_mulai) = CURDATE() 
             AND waktu_mulai IS NOT NULL 
             AND waktu_mulai != '' 
             AND waktu_mulai != '0000-00-00 00:00:00' 
             LIMIT 1"
        );
        if ($check_query && mysqli_num_rows($check_query) > 0) {
            // Karyawan sudah memulai istirahat hari ini, tidak bisa memulai lagi
            $_SESSION['error'] = 'Anda sudah memulai istirahat hari ini. Anda hanya bisa memulai istirahat sekali per hari.';
            header('Location: karyawan.php');
            exit;
        }
    }
    
    // Admin tidak dibatasi, karyawan yang belum istirahat hari ini bisa lanjut
    $durasi  = intval($_POST['durasi']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    mysqli_query($conn,
        "INSERT INTO istirahat (user_id, waktu_mulai, durasi, status, catatan)
         VALUES ($user_id, NOW(), $durasi, 'sedang', '$catatan')"
    ) or die(mysqli_error($conn));

    // redirect berdasarkan peran
    if ($role == 'admin') {
        header('Location: admin_break.php');
    } else {
        header('Location: karyawan.php');
    }
    exit;
}

// SELESAI MANUAL
if (isset($_GET['selesai'])) {
    $id = intval($_GET['selesai']);

    mysqli_query($conn,
        "UPDATE istirahat 
         SET status='selesai', waktu_selesai=NOW() 
         WHERE id=$id"
    ) or die(mysqli_error($conn));

    $row = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT user_id FROM istirahat WHERE id=$id")
    );

    if ($row) {
        $uid = intval($row['user_id']);
        $pesan = 'Waktu istirahat Anda telah berakhir.';

        mysqli_query($conn,
            "INSERT INTO notifikasi (id_karyawan, pesan)
             VALUES ($uid, '".mysqli_real_escape_string($conn,$pesan)."')"
        );
    }

    // redirect sesuai role
    if ($role == 'admin') {
        header('Location: admin_break.php');
    } else {
        header('Location: karyawan.php');
    }
    exit;
}

// AUTO SELESAI
if (isset($_GET['auto_selesai'])) {
    $id = intval($_GET['auto_selesai']);

    mysqli_query($conn,
        "UPDATE istirahat 
         SET status='selesai', waktu_selesai=NOW(), otomatis_selesai=1 
         WHERE id=$id"
    ) or die(mysqli_error($conn));

    $row = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT user_id FROM istirahat WHERE id=$id")
    );

    if ($row) {
        $uid = intval($row['user_id']);
        $pesan = 'Waktu istirahat Anda telah habis (selesai otomatis).';

        mysqli_query($conn,
            "INSERT INTO notifikasi (id_karyawan, pesan)
             VALUES ($uid, '".mysqli_real_escape_string($conn,$pesan)."')"
        );
        
        // Kirim FCM notification
        require_once 'send_fcm_notification.php';
        sendFCMNotification(
            $uid,
            'Waktu Istirahat Habis',
            $pesan,
            ['type' => 'istirahat_selesai', 'istirahat_id' => $id]
        );
    }

    echo json_encode(['status' => 'ok']);
    exit;
}
?>
