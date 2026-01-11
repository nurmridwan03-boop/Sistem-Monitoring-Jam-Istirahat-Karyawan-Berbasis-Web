<?php
session_start();
include 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('Location: index.php'); exit; }
$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"><title>Notifikasi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="bstrep/image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="bstrep/css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="assets/sidebar-style.css">
        <style>
        /* Navbar Styling */
        .navbar {
            border-bottom: 3px solid rgba(255,255,255,0.2);
        }
        
        /* Notifikasi Styling */
        .list-group-item {
            transition: background-color 0.2s ease;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
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
                    <a class="list-group-item list-group-item-action" href="karyawan.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="list-group-item list-group-item-action" href="status_karyawan.php"><i class="bi bi-people"></i> Status Karyawan</a>
                    <a class="list-group-item list-group-item-action" href="profile.php"><i class="bi bi-person-circle"></i> Profil</a>
                    <a class="list-group-item list-group-item-action" href="riwayat_istirahat.php"><i class="bi bi-clock-history"></i> Riwayat Istirahat</a>
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
                <i class="bi bi-bell-fill me-2"></i>
                Notifikasi
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
  <!-- Notifikasi Terbaru Section -->
  <div class="card shadow-sm">
    <div class="card-header" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white;">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="bi bi-bell-fill me-2"></i>Notifikasi Terbaru
        </h5>
        <div class="d-flex gap-2">
          <button id="cacheBtn" class="btn btn-warning btn-sm">
            <i class="bi bi-database me-1"></i>Cache
          </button>
          <button id="clearNotifBtn" class="btn btn-light btn-sm">
            <i class="bi bi-trash me-1"></i>Bersihkan Semua
          </button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div id="notifList"></div>
    </div>
  </div>
</div>

<div class="content" id="content">
<script>
function loadNotifikasi(){ 
    fetch('get_notifikasi_admin.php').then(r=>r.text()).then(html=>{ 
        document.getElementById('notifList').innerHTML = html; 
    }); 
}

// Jalankan load notifikasi setiap 10 detik untuk update realtime
setInterval(loadNotifikasi, 10000);
// Jalankan sekali saat halaman dimuat
loadNotifikasi();

document.getElementById("clearNotifBtn").onclick = function() {
    if (confirm("Yakin ingin menghapus semua notifikasi?")) {
        fetch('clear_notifikasi.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("Semua notifikasi berhasil dibersihkan.");
                loadNotifikasi(); // refresh daftar notifikasi
            } else {
                alert("Gagal menghapus notifikasi: " + data.msg);
            }
        });
    }
};

document.getElementById("cacheBtn").onclick = function() {
    if (confirm("Yakin ingin menghapus cache data istirahat dengan waktu_mulai invalid (0000-00-00 00:00:00)?")) {
        fetch('clear_cache_istirahat.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert(data.msg);
            } else if (data.status === "info") {
                alert(data.msg);
            } else {
                alert("Gagal menghapus cache: " + data.msg);
            }
        })
        .catch(error => {
            alert("Terjadi kesalahan: " + error);
        });
    }
};
</script>
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

</body></html>

