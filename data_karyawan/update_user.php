<?php
session_start();
include 'db.php';

// Pastikan hanya admin yang bisa update
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Akses ditolak. Hanya admin yang bisa mengubah data.';
    header('Location: users.php');
    exit;
}

// Validasi ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['error'] = 'ID tidak valid.';
    header('Location: users.php');
    exit;
}

// Escape semua input untuk keamanan
$id      = intval($_POST['id']);
$nama    = mysqli_real_escape_string($conn, $_POST['nama']);
$jk      = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
$umur    = intval($_POST['umur']);
$divisi  = mysqli_real_escape_string($conn, $_POST['divisi']);
$alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
$email   = mysqli_real_escape_string($conn, $_POST['email']);
$nomerHP = mysqli_real_escape_string($conn, $_POST['nomerHP'] ?? '');
$role    = mysqli_real_escape_string($conn, $_POST['role']);
$usernm  = mysqli_real_escape_string($conn, $_POST['username']);
$pass    = !empty($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

// Validasi role
if ($role != 'admin' && $role != 'karyawan') {
    $role = 'karyawan';
}

// Cek foto lama
$result = mysqli_query($conn, "SELECT foto FROM users WHERE id=$id");
if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = 'Data tidak ditemukan!';
    header('Location: users.php');
    exit;
}
$row = mysqli_fetch_assoc($result);
$fotoLama = $row['foto'];

$fotoBaru = $fotoLama;

// Jika upload foto baru
if (!empty($_FILES['foto']['name'])) {
    $fotoBaru = time() . "_" . $_FILES['foto']['name'];
    $target = "uploads/" . $fotoBaru;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        // hapus foto lama jika ada
        if (!empty($fotoLama) && file_exists("uploads/" . $fotoLama)) {
            unlink("uploads/" . $fotoLama);
        }
    } else {
        $_SESSION['error'] = 'Gagal mengupload foto.';
        header("Location: edit_user.php?id=$id");
        exit;
    }
}

// Query update
if (!empty($pass)) {
    $sql = "UPDATE users SET 
                nama='$nama',
                jenis_kelamin='$jk',
                umur=$umur,
                divisi='$divisi',
                alamat='$alamat',
                email='$email',
                nomerHP='$nomerHP',
                role='$role',
                username='$usernm',
                password='$pass',
                foto='$fotoBaru'
            WHERE id=$id";
} else {
    $sql = "UPDATE users SET 
                nama='$nama',
                jenis_kelamin='$jk',
                umur=$umur,
                divisi='$divisi',
                alamat='$alamat',
                email='$email',
                nomerHP='$nomerHP',
                role='$role',
                username='$usernm',
                foto='$fotoBaru'
            WHERE id=$id";
}

if (!mysqli_query($conn, $sql)) {
    $error_message = mysqli_error($conn);
    
    // Cek jenis error
    if (strpos($error_message, 'Duplicate entry') !== false) {
        if (strpos($error_message, 'username') !== false) {
            $_SESSION['error'] = 'Gagal mengupdate! Username sudah digunakan. Silakan gunakan username lain.';
        } else {
            $_SESSION['error'] = 'Gagal mengupdate! Data yang Anda masukkan sudah ada di database.';
        }
    } else {
        $_SESSION['error'] = 'Gagal mengupdate data: ' . $error_message;
    }
    
    header("Location: edit_user.php?id=$id");
    exit;
}

$_SESSION['success'] = 'Data karyawan berhasil diupdate!';
header("Location: users.php");
exit;
?>
