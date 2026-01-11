<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'karyawan') { header('Location: index.php'); exit; }
$user_id = intval($_SESSION['user_id']);
$nama = htmlspecialchars($_SESSION['nama']);

$active_query = mysqli_query($conn, "SELECT * FROM istirahat WHERE user_id=$user_id AND status='sedang' ORDER BY id DESC LIMIT 1");
$active = false;
if ($active_query) {
    $active = mysqli_fetch_assoc($active_query);
}

// Cek apakah hari ini sudah ada data istirahat untuk user tersebut
$today_rested_query = mysqli_query($conn, 
    "SELECT id FROM istirahat 
     WHERE user_id=$user_id 
     AND DATE(waktu_mulai) = CURDATE() 
     AND waktu_mulai IS NOT NULL 
     AND waktu_mulai != '' 
     AND waktu_mulai != '0000-00-00 00:00:00' 
     LIMIT 1"
);
$today_rested = false;
if ($today_rested_query && mysqli_num_rows($today_rested_query) > 0) {
    $today_rested = true;
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"><title>Karyawan - Monitoring Istirahat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="bstrep/image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="bstrep/css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="assets/sidebar-style.css">
        <style>
        .status-badge{min-width:110px;display:inline-block}
        
        /* Navbar Styling */
        .navbar {
            border-bottom: 3px solid rgba(255,255,255,0.2);
        }
        
        /* Card Styling */
        .card-modern {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card-modern:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }
        
        /* Countdown Timer Styling */
        .countdown-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin: 1rem 0;
        }
        
        .countdown-time {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
        }
        
        .countdown-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .countdown-units {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }
        
        .countdown-unit {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 10px;
            min-width: 100px;
        }
        
        .countdown-unit-value {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }
        
        .countdown-unit-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }
        
        /* Form Styling */
        .form-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 2rem;
        }
        
        .btn-primary-modern {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }
        
        .form-control-modern, .form-select-modern {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus, .form-select-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .status-card-active {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border-left: 4px solid #ffc107;
        }
        
        .status-card-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border-left: 4px solid #0dcaf0;
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
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="dashboard.php">Dashboard</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="profile.php">Profil</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="data_karyawan/users.php">data karyawan</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="admin_break.php">Mulai istirahat</a>
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
                <i class="bi bi-cup-hot me-2"></i>
                Istirahat Karyawan
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
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-clock-history me-2"></i>Status Istirahat</h4>
</div>

<?php if ($active) {
  date_default_timezone_set('Asia/Jakarta'); // waktu lokal Indonesia
  
  // Hitung timestamp untuk countdown (menggunakan timestamp untuk akurasi)
  $start_timestamp = strtotime($active['waktu_mulai']);
  $durasi = intval($active['durasi']);
  $end_timestamp = $start_timestamp + ($durasi * 60); // waktu berakhir dalam detik
  $now_timestamp = time();
  $remaining_seconds = max(0, $end_timestamp - $now_timestamp);
  
  // Format waktu untuk display
  $start_iso = date('c', $start_timestamp);
  $end_iso = date('c', $end_timestamp);
  
  $id_active = intval($active['id']);
?>
<div class="card card-modern status-card-active mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="flex-shrink-0 me-3">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-1"><i class="bi bi-check-circle-fill me-2 text-warning"></i>Anda Sedang Istirahat</h5>
                <p class="text-muted mb-0">
                    <i class="bi bi-clock me-1"></i>
                    Dimulai: <strong><?= htmlspecialchars($active['waktu_mulai']) ?></strong>
                </p>
            </div>
        </div>
        
        <div class="countdown-container">
            <div class="countdown-label mb-3"><i class="bi bi-stopwatch me-2"></i>Sisa Waktu Istirahat</div>
            <div id="countdown" class="countdown-units"></div>
        </div>
    </div>
</div>

<script>
// client countdown and fallback AJAX checker (every 30s)
// Deklarasi variabel countdownFinished sebelum digunakan
let countdownFinished = false; // Flag untuk menandai apakah countdown sudah habis

// Gunakan timestamp dari server untuk akurasi (menghindari masalah timezone)
const serverEndTimestamp = <?= $end_timestamp ?>; // Timestamp akhir dari server (dalam detik)
const serverStartTimestamp = <?= $start_timestamp ?>; // Timestamp mulai dari server
const countdownEl = document.getElementById('countdown');

function updateCountdown(){
  // Dapatkan timestamp client saat ini (dalam detik)
  const clientNowTimestamp = Math.floor(Date.now() / 1000);
  
  // Hitung selisih waktu (dalam detik)
  let diff = serverEndTimestamp - clientNowTimestamp;
  
  // Jika waktu sudah habis (dengan toleransi kecil untuk sync)
  if(diff <= 0){
    countdownEl.innerHTML = '<div class="countdown-time">Waktu istirahat sudah habis</div>'; 
    countdownFinished = true; // Tandai bahwa countdown sudah habis
    clearInterval(timer);
    // Tunggu minimal 1 detik setelah waktu habis baru kirim notifikasi dan selesaikan istirahat
    // Ini memastikan notifikasi hanya terkirim SETELAH hitungan mundur benar-benar habis
    setTimeout(function(){
      fetch('process.php?auto_selesai=<?= $id_active ?>').then(()=>{ 
        // Notifikasi akan dikirim oleh process.php setelah status diupdate
        alert('Waktu istirahat Anda telah selesai.'); 
        setTimeout(()=>{ location.reload(); },800); 
      });
    }, 1000);
    return;
  }
  
  // Hitung jam, menit, detik dari selisih waktu (dalam detik)
  const hours = Math.floor(diff / 3600);
  const minutes = Math.floor((diff % 3600) / 60);
  const seconds = diff % 60;
  
  let html = '';
  if (hours > 0) {
    html += '<div class="countdown-unit"><span class="countdown-unit-value">' + String(hours).padStart(2, '0') + '</span><span class="countdown-unit-label">Jam</span></div>';
  }
  html += '<div class="countdown-unit"><span class="countdown-unit-value">' + String(minutes).padStart(2, '0') + '</span><span class="countdown-unit-label">Menit</span></div>';
  html += '<div class="countdown-unit"><span class="countdown-unit-value">' + String(seconds).padStart(2, '0') + '</span><span class="countdown-unit-label">Detik</span></div>';
  
  countdownEl.innerHTML = html;
}
updateCountdown();
const timer = setInterval(updateCountdown, 1000);

// fallback: check server-side every 30s (default interval)
// Hanya cek jika countdown masih berjalan (belum habis)
setInterval(function(){
  if(countdownFinished) return; // Jangan cek lagi jika sudah selesai
  fetch('check_auto_finish.php').then(r=>r.json()).then(data=>{
    if(data.status === 'finished'){ 
      countdownFinished = true;
      clearInterval(timer); // Hentikan countdown timer
      alert('Waktu istirahat Anda telah selesai.'); 
      location.reload(); 
    }
  });
}, 30000);

// polling notifications every 10s
// PENTING: HANYA ambil notifikasi jika countdown sudah habis (setelah waktu istirahat benar-benar selesai)
// Tidak mengambil notifikasi sebelum countdown habis untuk menghindari notifikasi dikirim terlalu cepat
let notificationInterval = setInterval(function(){
  // Hanya ambil notifikasi jika countdown sudah selesai (waktu istirahat sudah habis)
  if(countdownFinished) {
    fetch('get_notifikasi_karyawan.php').then(r=>r.json()).then(data=>{ 
      if(data.length>0){ 
        data.forEach(n=>{ alert(n.pesan); }); 
      } 
    });
  } else {
    // Jika countdown masih berjalan, jangan ambil notifikasi sama sekali
    // Ini mencegah notifikasi terkirim sebelum waktu istirahat benar-benar habis
  }
}, 10000);
</script>

<?php } else { 
    // Cek apakah sudah ada istirahat hari ini
    if ($today_rested) { ?>
        <div class="card card-modern status-card-info">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2"><i class="bi bi-exclamation-triangle-fill me-2 text-info"></i>Sudah Memulai Istirahat Hari Ini</h5>
                        <p class="mb-0">Anda hanya bisa memulai istirahat sekali per hari. Silakan coba lagi besok atau tunggu admin mereset status Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="card card-modern form-card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-cup-hot" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Mulai Istirahat</h4>
                    <p class="text-muted">Silakan pilih durasi dan isi catatan (opsional)</p>
                </div>
                
                <form method="POST" action="process.php" id="formMulaiIstirahat" onsubmit="return confirmMulaiIstirahat()">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-clock me-2"></i>Durasi Istirahat (menit)
                        </label>
                        <input type="number" name="durasi" class="form-control form-control-modern" min="1" max="480" value="60" required placeholder="Masukkan durasi dalam menit">
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Contoh: 60 menit = 1 jam, 120 menit = 2 jam. Minimum 1 menit, maksimum 480 menit (8 jam).
                        </small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-sticky me-2"></i>Catatan (opsional)
                        </label>
                        <input type="text" name="catatan" class="form-control form-control-modern" placeholder="Masukkan catatan jika ada...">
                    </div>
                    <div class="text-center">
                        <button type="submit" name="mulai" class="btn btn-primary-modern btn-lg">
                            <i class="bi bi-play-circle-fill me-2"></i>Mulai Istirahat
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        function confirmMulaiIstirahat() {
            return confirm('Apakah Anda yakin memulai istirahat?');
        }
        </script>
    <?php } 
} ?>
<!--
<hr><h5>Riwayat Istirahat Anda</h5>
<table class="table table-striped"><thead><tr><th>Mulai</th><th>Selesai</th><th>Durasi</th><th>Catatan</th></tr></thead><tbody>
<?php 
$q = mysqli_query($conn, "SELECT * FROM istirahat WHERE user_id=$user_id ORDER BY id DESC"); 
if ($q) {
    while($r = mysqli_fetch_assoc($q)){
        echo '<tr><td>'.htmlspecialchars($r['waktu_mulai']).'</td><td>'.($r['waktu_selesai']?htmlspecialchars($r['waktu_selesai']):'-').'</td><td>'.($r['durasi']?intval($r['durasi']):'-').'</td><td>'.($r['catatan']?htmlspecialchars($r['catatan']):'-').'</td></tr>';
    }
} else {
    echo '<tr><td colspan="4" class="text-danger">Error: ' . mysqli_error($conn) . '</td></tr>';
}
?>-->
</tbody></table>
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