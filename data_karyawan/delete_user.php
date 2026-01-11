<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang bisa menghapus
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Akses ditolak. Hanya admin yang bisa menghapus data.';
    header('Location: users.php');
    exit;
}

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID tidak valid.';
    header('Location: users.php');
    exit;
}

$user_id = intval($_GET['id']);

// Mulai transaction untuk memastikan semua operasi berhasil atau tidak sama sekali
mysqli_begin_transaction($conn);

try {
    // 1. Hapus data istirahat terkait
    mysqli_query($conn, "DELETE FROM istirahat WHERE user_id = $user_id");
    
    // 2. Hapus data notifikasi terkait
    mysqli_query($conn, "DELETE FROM notifikasi WHERE id_karyawan = $user_id");
    
    // 3. Hapus data fcm_tokens terkait (jika ada, meskipun sudah ON DELETE CASCADE)
    mysqli_query($conn, "DELETE FROM fcm_tokens WHERE user_id = $user_id");
    
    // 4. Hapus user dari tabel users
    $delete_result = mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
    
    if (!$delete_result) {
        throw new Exception('Gagal menghapus data user: ' . mysqli_error($conn));
    }
    
    // Commit transaction jika semua berhasil
    mysqli_commit($conn);
    
    $_SESSION['success'] = 'Data karyawan berhasil dihapus beserta semua data terkait.';
    
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($conn);
    $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
}

// Redirect kembali ke halaman users
header('Location: users.php');
exit;
?>