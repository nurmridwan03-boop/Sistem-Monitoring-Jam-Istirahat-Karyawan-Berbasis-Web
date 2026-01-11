<?php
session_start();
include 'db.php';

// Cek apakah user login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query);

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama       = $_POST['nama'];
    $umur       = $_POST['umur'];
    $jk         = $_POST['jenis_kelamin'];
    $alamat     = $_POST['alamat'];
    $email      = $_POST['email'];

    // Update password jika diisi
    $passQuery = "";
    if (!empty($_POST['password'])) {
        $pass = $_POST['password']; // TANPA ENKRIPSI SESUAI PERMINTAAN SEBELUMNYA
        $passQuery = ", password = '$pass'";
    }

    // Upload foto jika ada
    $fotoQuery = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "data_karyawan/uploads/" . $foto);
        $fotoQuery = ", foto = '$foto'";
    }

    // Update ke database
    $sql = "UPDATE users SET 
                nama='$nama',
                umur='$umur',
                jenis_kelamin='$jk',
                alamat='$alamat',
                email='$email'
                $passQuery
                $fotoQuery
            WHERE id=$user_id";

    mysqli_query($conn, $sql);

    header("Location: profile.php?update=success");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="bstrep/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/sidebar-style.css">
    <style>
        /* Navbar Styling */
        .navbar {
            border-bottom: 3px solid rgba(255,255,255,0.2);
        }
        
        /* Profile Card Styling */
        .profile-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: #5DADE2;
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header-bg {
            position: absolute;
            top: -50px;
            left: -50px;
            right: -50px;
            bottom: -50px;
            background-size: cover;
            background-position: center;
            opacity: 0.2;
            filter: blur(30px);
            z-index: 0;
        }
        
        .profile-header-content {
            position: relative;
            z-index: 1;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }
        
        .profile-role {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .form-card {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
        }
        
        .form-label-modern {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control-modern, .form-select-modern {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus, .form-select-modern:focus {
            border-color: #5DADE2;
            box-shadow: 0 0 0 0.2rem rgba(93, 173, 226, 0.25);
        }
        
        .form-control-modern:disabled {
            background-color: #f8f9fa;
            opacity: 0.7;
        }
        
        .btn-primary-modern {
            background: #5DADE2;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white !important;
        }
        
        .btn-primary-modern:hover {
            background: #3498DB;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(93, 173, 226, 0.4);
            color: white !important;
        }
        
        .btn-secondary-modern {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-secondary-modern:hover {
            background: linear-gradient(135deg, #5a6268 0%, #6c757d 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
            color: white;
        }
        
        .info-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #5DADE2;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
    </style>
</head>
<body>
    <?php $role = $_SESSION['role']; ?>
    <div class="d-flex" id="wrapper">
            <!-- Sidebar-->
            <div id="sidebar-wrapper">
                <div class="sidebar-heading">Monitoring Istirahat</div>
                <div class="list-group list-group-flush">
                    <?php if ($role == 'karyawan') { ?>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'karyawan.php' ? 'active' : '' ?>" href="karyawan.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'status_karyawan.php' ? 'active' : '' ?>" href="status_karyawan.php"><i class="bi bi-people"></i> Status Karyawan</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>" href="profile.php"><i class="bi bi-person-circle"></i> Profil</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'riwayat_istirahat.php' ? 'active' : '' ?>" href="riwayat_istirahat.php"><i class="bi bi-clock-history"></i> Riwayat Istirahat</a>
                    <?php } ?>

                    <?php if ($role == 'admin') { ?>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>" href="profile.php"><i class="bi bi-person-circle"></i> Profil</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>" href="data_karyawan/users.php"><i class="bi bi-people-fill"></i> Data Karyawan</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'admin_break.php' ? 'active' : '' ?>" href="admin_break.php"><i class="bi bi-cup-hot"></i> Mulai Istirahat</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'riwayat_istirahat.php' ? 'active' : '' ?>" href="riwayat_istirahat.php"><i class="bi bi-clock-history"></i> Riwayat Istirahat Saya</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'riwayat_istirahat_admin.php' ? 'active' : '' ?>" href="riwayat_istirahat_admin.php"><i class="bi bi-journal-text"></i> Riwayat Semua Karyawan</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'notifikasi.php' ? 'active' : '' ?>" href="notifikasi.php"><i class="bi bi-bell-fill"></i> Notifikasi</a>
                    <?php } ?>
                </div>
            </div>
            <!-- Page content wrapper-->
            <div id="page-content-wrapper">
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light me-3" id="sidebarToggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h4 d-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                Profil Saya
            </span>
        </div>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle me-1"></i>
                <?= $nama ?>
            </span>
            <a href="process.php?logout" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>
<div class="container-fluid">
<div class="container mt-4">

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-bg" style="background-image: url('data_karyawan/uploads/<?= $user['foto'] ?>');"></div>
                <div class="profile-header-content">
                <div class="position-relative d-inline-block">
                    <img src="data_karyawan/uploads/<?= $user['foto'] ?>" 
                         class="profile-avatar" 
                         alt="Foto Profil">
                    <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2" style="border: 3px solid white;">
                        <i class="bi bi-camera-fill text-primary"></i>
                    </div>
                </div>
                <div class="profile-name"><?= htmlspecialchars($user['nama']) ?></div>
                <div class="profile-role">
                    <i class="bi bi-shield-check me-1"></i>
                    <?= ucfirst($user['role']) ?>
                </div>
                </div>
            </div>

            <!-- Profile Body -->
            <div class="form-card">
                <?php if (isset($_GET['update'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Profil berhasil diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <!-- Info Section (Read Only) -->
                    <div class="info-section mb-4">
                        <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Akun</h5>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-hash"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">ID Pengguna</small>
                                <strong><?= $user['id'] ?></strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Username</small>
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Editable Form Fields -->
                    <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Profil</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-modern">
                                <i class="bi bi-person"></i>Nama
                            </label>
                            <input type="text" name="nama" class="form-control form-control-modern" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-modern">
                                <i class="bi bi-calendar"></i>Umur
                            </label>
                            <input type="number" name="umur" class="form-control form-control-modern" value="<?= htmlspecialchars($user['umur']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-modern">
                                <i class="bi bi-gender-ambiguous"></i>Jenis Kelamin
                            </label>
                            <select name="jenis_kelamin" class="form-select form-select-modern" required>
                                <option <?= $user['jenis_kelamin']=='Laki-laki'?'selected':'' ?>>Laki-laki</option>
                                <option <?= $user['jenis_kelamin']=='Perempuan'?'selected':'' ?>>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-modern">
                                <i class="bi bi-envelope"></i>Email
                            </label>
                            <input type="email" name="email" class="form-control form-control-modern" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-modern">
                            <i class="bi bi-geo-alt"></i>Alamat
                        </label>
                        <textarea name="alamat" class="form-control form-control-modern" rows="3" required><?= htmlspecialchars($user['alamat']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-modern">
                            <i class="bi bi-lock"></i>Password Baru
                            <small class="text-muted">(kosongkan jika tidak ingin ganti)</small>
                        </label>
                        <input type="password" name="password" class="form-control form-control-modern" placeholder="Masukkan password baru">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-modern">
                            <i class="bi bi-image"></i>Ganti Foto Profil
                        </label>
                        <input type="file" name="foto" id="foto" class="form-control form-control-modern" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                        <?php 
                        $redirect_url = ($_SESSION['role'] == 'admin') ? 'dashboard.php' : 'karyawan.php';
                        ?>
                        <a href="<?= $redirect_url ?>" class="btn btn-secondary-modern">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>

</div>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="bstrep/js/scripts.js"></script>
        
        <!-- Firebase SDK -->
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js"></script>
        <!-- FCM Setup Script - Notifikasi akan muncul di semua halaman -->
        <script src="assets/fcm-setup.js"></script>


</body>
