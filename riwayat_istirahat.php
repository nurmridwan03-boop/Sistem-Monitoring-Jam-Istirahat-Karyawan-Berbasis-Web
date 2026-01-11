<?php
session_start();
include 'db.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Ambil ID user yang login
$user_id = intval($_SESSION['user_id']);
$nama = htmlspecialchars($_SESSION['nama'] ?? 'User');
$role = $_SESSION['role'] ?? '';

// Hapus semua riwayat jika tombol ditekan (kecuali 30 data terbaru)
if (isset($_POST['hapus_semua'])) {
    // Ambil 30 ID terbaru untuk dilindungi
    $protected_ids_query = mysqli_query($conn, 
        "SELECT id FROM riwayat_istirahat 
         WHERE user_id = $user_id 
         ORDER BY id DESC 
         LIMIT 30"
    );
    
    $protected_ids = [];
    if ($protected_ids_query) {
        while ($row = mysqli_fetch_assoc($protected_ids_query)) {
            $protected_ids[] = intval($row['id']);
        }
    }
    
    if (!empty($protected_ids)) {
        // Hapus semua data kecuali 30 terbaru
        $ids_string = implode(',', $protected_ids);
        mysqli_query($conn, 
            "DELETE FROM riwayat_istirahat 
             WHERE user_id = $user_id 
             AND id NOT IN ($ids_string)"
        );
        $msg = "Riwayat telah dihapus! (30 data terbaru tidak dapat di hapus)";
    } else {
        // Jika tidak ada data, tidak ada yang dihapus
        $msg = "Tidak ada data yang bisa dihapus.";
    }
}

// Ambil riwayat dari tabel riwayat_istirahat
// Hanya ambil data yang memiliki waktu_mulai valid (bukan NULL atau '0000-00-00 00:00:00')
$q = mysqli_query($conn, "SELECT * FROM riwayat_istirahat 
                          WHERE user_id=$user_id 
                          AND waktu_mulai IS NOT NULL 
                          AND waktu_mulai != '0000-00-00 00:00:00' 
                          AND waktu_mulai != '0000-00-00'
                          ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title>Riwayat Istirahat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="bstrep/css/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/sidebar-style.css">
<link rel="icon" type="bstrep/image/x-icon" href="assets/favicon.ico" />
<style>
    /* Navbar Styling */
    .navbar {
        border-bottom: 3px solid rgba(255,255,255,0.2);
    }
    
    /* Table Header Soft Styling */
    .table-header-soft {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
    }
    
    .table-header-soft th {
        border: 1px solid rgba(255,255,255,0.2) !important;
        font-weight: 600;
    }
    
    .table-bordered {
        border: 2px solid #000 !important;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
    }
    
    /* Card Styling */
    .card-modern {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    /* Button Styling */
    .btn-danger-modern {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }
    
    .btn-danger-modern:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        color: white !important;
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
                <i class="bi bi-clock-history me-2"></i>
                <?= $role == 'admin' ? 'Riwayat Istirahat Saya' : 'Riwayat Istirahat' ?>
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


    <?php if (isset($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-clock-history me-2"></i>Riwayat Istirahat</h4>
        <form method="post">
            <button onclick="return confirm('Hapus semua riwayat istirahat? (30 data terbaru tidak dapat di hapus)')"
                    type="submit" name="hapus_semua"
                    class="btn btn-danger-modern">
                <i class="bi bi-trash me-2"></i>Hapus Semua Riwayat
            </button>
        </form>
    </div>

    <div class="card card-modern shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center align-middle mb-0">
                    <thead class="table-header-soft">
                        <tr>
                            <th class="text-white">Mulai</th>
                            <th class="text-white">Selesai</th>
                            <th class="text-white">Durasi (Menit)</th>
                            <th class="text-white">Catatan</th>
                        </tr>
                    </thead>

                <tbody>
                    <?php while($r = mysqli_fetch_assoc($q)): 
                        // Skip data yang tidak valid
                        if (empty($r['waktu_mulai']) || 
                            $r['waktu_mulai'] == '0000-00-00 00:00:00' || 
                            $r['waktu_mulai'] == '0000-00-00' ||
                            strtotime($r['waktu_mulai']) === false) {
                            continue;
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($r['waktu_mulai']) ?></td>
                            <td><?= $r['waktu_selesai'] && $r['waktu_selesai'] != '0000-00-00 00:00:00' && $r['waktu_selesai'] != '0000-00-00' ? htmlspecialchars($r['waktu_selesai']) : '-' ?></td>
                            <td><?= $r['durasi'] ? intval($r['durasi']) : '-' ?></td>
                            <td><?= $r['catatan'] ? htmlspecialchars($r['catatan']) : '-' ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
</html>
