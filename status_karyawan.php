<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'karyawan') { 
    header('Location: index.php'); 
    exit; 
}
$nama = htmlspecialchars($_SESSION['nama']);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"><title>Status Karyawan - Monitoring Istirahat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="bstrep/image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="bstrep/css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="assets/sidebar-style.css">
        <style>
        .status-badge{min-width:110px;display:inline-block}
        .table-bordered {
            border: 2px solid #000 !important;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000 !important;
        }
        
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
        
        /* Mobile Responsive Styles */
        @media (max-width: 767.98px) {
            /* Table responsive wrapper */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }
            
            /* Table styling untuk mobile */
            .table {
                font-size: 0.85rem;
                min-width: 700px; /* Minimum width untuk memastikan semua kolom terlihat */
            }
            
            .table th,
            .table td {
                padding: 0.5rem 0.4rem;
                white-space: nowrap; /* Mencegah text wrapping */
            }
            
            /* Search dan Sort Card untuk mobile */
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            /* Form controls untuk mobile */
            .form-control,
            .form-select {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }
            
            /* Input group buttons untuk mobile */
            .input-group .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            
            /* Status badge untuk mobile */
            .status-badge {
                min-width: 90px;
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            /* Search form untuk mobile */
            .row.g-3 > [class*="col-"] {
                margin-bottom: 1rem;
            }
            
            /* Container padding untuk mobile */
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            /* Text adjustments untuk mobile */
            h4 {
                font-size: 1.25rem !important;
            }
            
            .text-muted {
                font-size: 0.85rem;
            }
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
                <i class="bi bi-people me-2"></i>
                Status Karyawan (Realtime)
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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Status Karyawan (Realtime)</h4>
  </div>
  
  <!-- Search dan Sort -->
  <div class="card shadow mb-3">
    <div class="card-body">
      <form id="searchForm" class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label">Cari Karyawan</label>
          <div class="input-group">
            <input type="text" id="searchInput" name="search" class="form-control" 
                   placeholder="Cari berdasarkan nama, ID, divisi..." 
                   value="">
            <button class="btn btn-outline-primary" type="button" onclick="applySearch()">
              <i class="bi bi-search"></i> <span class="d-none d-sm-inline">Cari</span>
            </button>
            <button class="btn btn-outline-secondary" type="button" onclick="resetSearch()">
              <i class="bi bi-x-circle"></i> <span class="d-none d-sm-inline">Reset</span>
            </button>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Urutkan Berdasarkan</label>
          <select id="sortSelect" name="sort" class="form-select" onchange="applySearch()">
            <option value="nama">Nama</option>
            <option value="divisi">Divisi</option>
            <option value="status">Status</option>
            <option value="waktu_mulai">Waktu Mulai</option>
            <option value="durasi">Durasi</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Urutan</label>
          <select id="orderSelect" name="order" class="form-select" onchange="applySearch()">
            <option value="ASC">A-Z / Kecil ke Besar</option>
            <option value="DESC">Z-A / Besar ke Kecil</option>
          </select>
        </div>
      </form>
    </div>
  </div>
  
  <div id="karyawanList"></div>
</div>


<div class="content" id="content">
<script>
// Fungsi untuk mengecek dan menyelesaikan istirahat yang sudah habis (background)
function checkAllBreaks() {
    return fetch('auto_check_all_breaks.php')
        .then(res => res.json())
        .then(data => {
            // Jika ada istirahat yang selesai, return true untuk trigger reload
            if (data.finished > 0) {
                console.log('Istirahat selesai:', data.finished, data.message);
                return true;
            }
            return false;
        })
        .catch(err => {
            console.error('Error checking breaks:', err);
            return false;
        });
}

// Variabel global untuk search dan sort
var currentSearch = '';
var currentSort = 'nama';
var currentOrder = 'ASC';

function loadStatus(){ 
    // Build URL dengan parameter search dan sort
    var url = 'get_status_karyawan_view.php?';
    if (currentSearch) {
        url += 'search=' + encodeURIComponent(currentSearch) + '&';
    }
    url += 'sort=' + currentSort + '&order=' + currentOrder;
    
    fetch(url).then(r=>r.text()).then(html=>{ 
        document.getElementById('karyawanList').innerHTML = html; 
    }); 
}

function applySearch() {
    currentSearch = document.getElementById('searchInput').value;
    currentSort = document.getElementById('sortSelect').value;
    currentOrder = document.getElementById('orderSelect').value;
    loadStatus();
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    currentSearch = '';
    currentSort = 'nama';
    currentOrder = 'ASC';
    document.getElementById('sortSelect').value = 'nama';
    document.getElementById('orderSelect').value = 'ASC';
    loadStatus();
}

function sortTable(sortBy) {
    if (currentSort == sortBy) {
        // Toggle order jika kolom yang sama diklik
        currentOrder = (currentOrder == 'ASC') ? 'DESC' : 'ASC';
    } else {
        currentSort = sortBy;
        currentOrder = 'ASC';
    }
    document.getElementById('sortSelect').value = currentSort;
    document.getElementById('orderSelect').value = currentOrder;
    loadStatus();
}

// Event listener untuk Enter di search input
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applySearch();
            }
        });
    }
});

// Fungsi gabungan: cek istirahat dulu, lalu load status
function checkAndLoadStatus() {
    checkAllBreaks().then(needsReload => {
        if (needsReload) {
            // Jika ada istirahat yang selesai, tunggu sebentar lalu reload
            setTimeout(loadStatus, 500);
        } else {
            // Jika tidak ada yang selesai, tetap reload untuk update realtime
            loadStatus();
        }
    });
}

// Jalankan pengecekan dan load status setiap 10 detik untuk update lebih cepat dan akurat
setInterval(checkAndLoadStatus, 10000);
// Jalankan sekali saat halaman dimuat
checkAndLoadStatus();
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

