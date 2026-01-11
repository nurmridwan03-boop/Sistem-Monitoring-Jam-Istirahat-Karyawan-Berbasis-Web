<?php
session_start();
include 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('Location: index.php'); exit; }
$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"><title>Dashboard Admin</title>
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
        
        /* Mobile Responsive */
        @media (max-width: 767.98px) {
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .table-responsive {
                border: none;
            }
            
            .card {
                margin-bottom: 1rem;
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
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard Admin
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
    <div>
      <button id="resetAllBtn" class="btn btn-danger me-2">Reset Semua Status</button>
    </div>
  </div>
  
  <!-- Search dan Sort -->
  <div class="card shadow mb-3">
    <div class="card-body">
      <form id="searchForm" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Cari Karyawan</label>
          <div class="input-group">
            <input type="text" id="searchInput" name="search" class="form-control" 
                   placeholder="Cari berdasarkan nama, ID, divisi, atau role..." 
                   value="">
            <button class="btn btn-outline-primary" type="button" onclick="applySearch()">
              <i class="bi bi-search"></i> Cari
            </button>
            <button class="btn btn-outline-secondary" type="button" onclick="resetSearch()">
              <i class="bi bi-x-circle"></i> Reset
            </button>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Urutkan Berdasarkan</label>
          <select id="sortSelect" name="sort" class="form-select" onchange="applySearch()">
            <option value="nama">Nama</option>
            <option value="divisi">Divisi</option>
            <option value="role">Role</option>
            <option value="status">Status</option>
            <option value="waktu_mulai">Waktu Mulai</option>
            <option value="durasi">Durasi</option>
          </select>
        </div>
        <div class="col-md-3">
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
    var url = 'get_status_karyawan.php?';
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

// Fungsi reset status karyawan
function resetStatus(userId) {
    if (!confirm('Yakin ingin mereset status karyawan ini? Status akan kembali menjadi "Belum Istirahat" dan data mulai, selesai, serta durasi akan dikosongkan.')) {
        return;
    }
    
    fetch('reset_status_karyawan.php?user_id=' + userId)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Status berhasil direset!');
                // Reload status setelah 500ms untuk update realtime
                setTimeout(function() {
                    loadStatus();
                }, 500);
            } else {
                alert('Gagal mereset status: ' + data.msg);
            }
        })
        .catch(err => {
            alert('Error: ' + err);
        });
}

// Event delegation untuk tombol reset (karena tombol di-load via AJAX)
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('reset-btn')) {
        var userId = e.target.getAttribute('data-user-id');
        if (userId) {
            resetStatus(parseInt(userId));
        }
    }
});

// Fungsi reset semua status
function resetAllStatus() {
    if (!confirm('Yakin ingin mereset status SEMUA karyawan? Semua status akan kembali menjadi "Belum Istirahat" dan data mulai, selesai, serta durasi akan dikosongkan.')) {
        return;
    }
    
    fetch('reset_all_status.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.msg);
                // Reload status setelah 500ms untuk update realtime
                setTimeout(function() {
                    loadStatus();
                }, 500);
            } else {
                alert('Gagal mereset status: ' + data.msg);
            }
        })
        .catch(err => {
            alert('Error: ' + err);
        });
}

document.getElementById("resetAllBtn").onclick = function() {
    resetAllStatus();
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
        <!-- FCM Setup Script -->
        <script src="assets/fcm-setup.js"></script>

</body></html>