<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Pastikan hanya admin yang bisa reset
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

// Pastikan user_id diberikan
if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'user_id required']);
    exit;
}

$target_user_id = intval($_GET['user_id']);

// Reset status 1 karyawan: TIDAK menyimpan ke riwayat, hanya reset status saja
// Reset SEMUA record untuk user tersebut agar karyawan bisa mulai istirahat lagi
$result = mysqli_query($conn,
    "UPDATE istirahat 
     SET status=NULL, waktu_mulai=NULL, waktu_selesai=NULL, durasi=NULL 
     WHERE user_id=$target_user_id"
) or die(json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]));

// Jika tidak ada record sama sekali, buat record baru dengan status NULL (belum istirahat)
$check_query = mysqli_query($conn, "SELECT id FROM istirahat WHERE user_id=$target_user_id LIMIT 1");
if (!$check_query || mysqli_num_rows($check_query) == 0) {
    mysqli_query($conn,
        "INSERT INTO istirahat (user_id, status, waktu_mulai, waktu_selesai, durasi) 
         VALUES ($target_user_id, NULL, NULL, NULL, NULL)"
    ) or die(json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]));
}

echo json_encode(['status' => 'success', 'msg' => 'Status berhasil direset']);
?>

