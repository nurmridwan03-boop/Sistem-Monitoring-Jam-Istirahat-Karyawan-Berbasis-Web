<?php
session_start();
include 'db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Hapus semua data dari tabel istirahat yang memiliki waktu_mulai = '0000-00-00 00:00:00'
// Termasuk data terbaru jika memenuhi kondisi tersebut
$delete_query = "DELETE FROM istirahat 
                 WHERE waktu_mulai = '0000-00-00 00:00:00' 
                 OR waktu_mulai = '0000-00-00'
                 OR waktu_mulai IS NULL";
$delete_result = mysqli_query($conn, $delete_query);

if (!$delete_result) {
    echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus data: ' . mysqli_error($conn)]);
    exit;
}

$deleted_count = mysqli_affected_rows($conn);

if ($deleted_count > 0) {
    echo json_encode([
        'status' => 'success', 
        'msg' => "Cache berhasil dibersihkan! ($deleted_count data dengan waktu_mulai invalid telah dihapus)"
    ]);
} else {
    echo json_encode(['status' => 'info', 'msg' => 'Tidak ada data dengan waktu_mulai invalid untuk dihapus.']);
}

