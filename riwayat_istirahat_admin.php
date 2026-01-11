<?php
session_start();
include 'db.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');
date_default_timezone_set('Asia/Jakarta');

// Nama bulan (harus didefinisikan dulu untuk digunakan di logika hapus)
$nama_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Ambil parameter bulan dan tahun (default: bulan ini)
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Validasi bulan dan tahun
if ($bulan < 1 || $bulan > 12) $bulan = date('m');
if ($tahun < 2020 || $tahun > 2100) $tahun = date('Y');

// Ambil parameter sorting
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'waktu_mulai';
$sort_order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'DESC';

// Validasi sort_by untuk keamanan
$allowed_sort = ['nama', 'divisi', 'waktu_mulai', 'waktu_selesai', 'durasi'];
if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'waktu_mulai';
}

// Validasi sort_order
if ($sort_order != 'ASC' && $sort_order != 'DESC') {
    $sort_order = 'DESC';
}

// Mapping sort_by untuk query
$sort_mapping = [
    'nama' => 'u.nama',
    'divisi' => 'u.divisi',
    'waktu_mulai' => 'i.waktu_mulai',
    'waktu_selesai' => 'i.waktu_selesai',
    'durasi' => 'i.durasi'
];
$sort_column = $sort_mapping[$sort_by] ?? 'i.waktu_mulai';

// Hapus riwayat berdasarkan bulan dan tahun jika tombol ditekan
$msg = '';
if (isset($_POST['hapus_riwayat'])) {
    // Ambil bulan dan tahun dari POST, jika tidak ada ambil dari GET
    $bulan_hapus = isset($_POST['bulan']) ? intval($_POST['bulan']) : $bulan;
    $tahun_hapus = isset($_POST['tahun']) ? intval($_POST['tahun']) : $tahun;
    
    // Validasi bulan dan tahun
    if ($bulan_hapus < 1 || $bulan_hapus > 12) $bulan_hapus = date('m');
    if ($tahun_hapus < 2020 || $tahun_hapus > 2100) $tahun_hapus = date('Y');
    
    // Hapus semua riwayat untuk bulan dan tahun yang sedang difilter (termasuk 30 data terbaru)
    $delete_query = "DELETE FROM riwayat_istirahat 
                    WHERE MONTH(waktu_mulai) = $bulan_hapus 
                    AND YEAR(waktu_mulai) = $tahun_hapus";
    $delete_result = mysqli_query($conn, $delete_query);
    
    if ($delete_result) {
        header("Location: riwayat_istirahat_admin.php?bulan=$bulan_hapus&tahun=$tahun_hapus&msg=" . urlencode("Riwayat untuk bulan " . $nama_bulan[$bulan_hapus-1] . " $tahun_hapus telah dihapus!"));
        exit;
    } else {
        $msg = "Gagal menghapus riwayat: " . mysqli_error($conn);
    }
}

// Ambil pesan dari URL jika ada (setelah redirect)
if (isset($_GET['msg'])) {
    $msg = urldecode($_GET['msg']);
}

// Query untuk mendapatkan semua riwayat istirahat dengan data karyawan dari tabel riwayat_istirahat
// Hanya ambil data yang memiliki waktu_mulai valid (bukan NULL atau '0000-00-00 00:00:00')
$query = "SELECT i.*, u.nama, u.divisi 
          FROM riwayat_istirahat i 
          JOIN users u ON i.user_id = u.id 
          WHERE i.waktu_mulai IS NOT NULL 
          AND i.waktu_mulai != '0000-00-00 00:00:00' 
          AND i.waktu_mulai != '0000-00-00'
          AND MONTH(i.waktu_mulai) = $bulan 
          AND YEAR(i.waktu_mulai) = $tahun 
          ORDER BY $sort_column $sort_order";

$result = mysqli_query($conn, $query);

// Kelompokkan data berdasarkan hari
$data_per_hari = [];
$total_durasi_bulan = 0;
$total_istirahat_bulan = 0;
$total_otomatis = 0;
$total_manual = 0;

while ($row = mysqli_fetch_assoc($result)) {
    // Pastikan waktu_mulai valid sebelum diproses
    if (empty($row['waktu_mulai']) || 
        $row['waktu_mulai'] == '0000-00-00 00:00:00' || 
        $row['waktu_mulai'] == '0000-00-00' ||
        strtotime($row['waktu_mulai']) === false) {
        continue; // Skip data yang tidak valid
    }
    
    $tanggal = date('Y-m-d', strtotime($row['waktu_mulai']));
    
    if (!isset($data_per_hari[$tanggal])) {
        $data_per_hari[$tanggal] = [
            'data' => [],
            'total_durasi' => 0,
            'total_istirahat' => 0,
            'total_otomatis' => 0,
            'total_manual' => 0
        ];
    }
    
    $data_per_hari[$tanggal]['data'][] = $row;
    $data_per_hari[$tanggal]['total_durasi'] += intval($row['durasi'] ?? 0);
    $data_per_hari[$tanggal]['total_istirahat']++;
    
    if ($row['otomatis_selesai']) {
        $data_per_hari[$tanggal]['total_otomatis']++;
        $total_otomatis++;
    } else {
        $data_per_hari[$tanggal]['total_manual']++;
        $total_manual++;
    }
    
    $total_durasi_bulan += intval($row['durasi'] ?? 0);
    $total_istirahat_bulan++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Riwayat Istirahat Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="bstrep/image/x-icon" href="assets/favicon.ico" />
    <link href="bstrep/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/sidebar-style.css">
    <style>
        .day-section {
            margin-bottom: 2rem;
        }
        .day-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .summary-card {
            border-left: 4px solid #667eea;
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
                min-width: 800px; /* Minimum width untuk memastikan semua kolom terlihat */
            }
            
            .table th,
            .table td {
                padding: 0.5rem 0.4rem;
                white-space: nowrap; /* Mencegah text wrapping */
            }
            
            /* Day header untuk mobile */
            .day-header {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
            
            .day-header h5 {
                font-size: 1rem !important;
            }
            
            .day-header .badge {
                font-size: 0.75rem;
                margin-top: 0.5rem;
                display: block;
                width: fit-content;
            }
            
            /* Cards untuk mobile */
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
            
            /* Buttons untuk mobile */
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            
            .btn-sm {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }
            
            /* Filter form untuk mobile */
            .row.g-3 > [class*="col-"] {
                margin-bottom: 1rem;
            }
            
            /* Container padding untuk mobile */
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            /* Text adjustments untuk mobile */
            h5 {
                font-size: 1.1rem !important;
            }
            
            h3 {
                font-size: 1.5rem !important;
            }
            
            .text-muted {
                font-size: 0.85rem;
            }
            
            /* Summary card untuk mobile */
            .summary-card .row {
                flex-direction: column;
            }
            
            .summary-card .col-md-6 {
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
                            <i class="bi bi-journal-text me-2"></i>
                            Riwayat Semua Karyawan
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
                    
                    <?php if (isset($msg) && !empty($msg)): ?>
                        <div class="alert alert-<?= strpos($msg, 'Gagal') !== false ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filter Bulan dan Tahun -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Pilih Bulan</label>
                                    <form method="GET" action="riwayat_istirahat_admin.php" style="display: inline;">
                                        <select name="bulan" class="form-select" onchange="this.form.submit()">
                                            <?php for($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>>
                                                    <?= $nama_bulan[$i-1] ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <input type="hidden" name="tahun" value="<?= $tahun ?>">
                                    </form>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Pilih Tahun</label>
                                    <form method="GET" action="riwayat_istirahat_admin.php" style="display: inline;">
                                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                                            <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                                                <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>>
                                                    <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <input type="hidden" name="bulan" value="<?= $bulan ?>">
                                    </form>
                                </div>
                                <div class="col-12 col-md-2 d-flex align-items-end">
                                    <a href="riwayat_istirahat_admin.php" class="btn btn-secondary w-100">
                                        <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">Reset</span>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4 d-flex align-items-end gap-2 flex-wrap">
                                    <form method="POST" action="riwayat_istirahat_admin.php" style="display: inline; flex: 1; min-width: 140px;" onsubmit="return confirm('Yakin ingin menghapus riwayat untuk bulan <?= $nama_bulan[$bulan-1] ?> <?= $tahun ?>?')">
                                        <input type="hidden" name="bulan" value="<?= $bulan ?>">
                                        <input type="hidden" name="tahun" value="<?= $tahun ?>">
                                        <button type="submit" name="hapus_riwayat" class="btn btn-danger w-100">
                                            <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Hapus Riwayat</span><span class="d-sm-none">Hapus</span>
                                        </button>
                                    </form>
                                    <a href="export_excel_riwayat.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success" style="flex: 0.5; min-width: 60px;">
                                        <i class="bi bi-file-earmark-excel"></i> <span class="d-none d-md-inline">Excel</span>
                                    </a>
                                    <a href="export_csv_riwayat.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-primary" style="flex: 0.5; min-width: 60px;">
                                        <i class="bi bi-file-earmark-spreadsheet"></i> <span class="d-none d-md-inline">CSV</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rekap Bulanan -->
                    <div class="card shadow mb-4 summary-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Rekap Bulan <?= $nama_bulan[$bulan-1] ?> <?= $tahun ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="p-3">
                                        <h3 class="text-primary"><?= $total_istirahat_bulan ?></h3>
                                        <p class="text-muted mb-0">Total Istirahat</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3">
                                        <h3 class="text-success"><?= number_format($total_durasi_bulan / 60, 1) ?></h3>
                                        <p class="text-muted mb-0">Total Durasi (Jam)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data per Hari -->
                    <?php if (empty($data_per_hari)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Tidak ada data istirahat untuk bulan <?= $nama_bulan[$bulan-1] ?> <?= $tahun ?>
                        </div>
                    <?php else: ?>
                        <?php 
                        // Urutkan berdasarkan tanggal (terbaru dulu)
                        krsort($data_per_hari);
                        foreach ($data_per_hari as $tanggal => $hari_data): 
                            $nama_hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $hari_index = date('w', strtotime($tanggal));
                            $tanggal_formatted = date('d', strtotime($tanggal));
                        ?>
                            <div class="day-section">
                                <div class="day-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-calendar-event"></i> 
                                        <?= $nama_hari[$hari_index] ?>, <?= $tanggal_formatted ?> <?= $nama_bulan[$bulan-1] ?> <?= $tahun ?>
                                        <span class="badge bg-light text-dark ms-2">
                                            <?= $hari_data['total_istirahat'] ?> istirahat | 
                                            <?= number_format($hari_data['total_durasi'] / 60, 1) ?> jam
                                        </span>
                                    </h5>
                                </div>
                                
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>
                                                        <a href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&sort=nama&order=<?= ($sort_by == 'nama' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?>" 
                                                           class="text-dark text-decoration-none">
                                                            Nama
                                                            <?php if ($sort_by == 'nama'): ?>
                                                                <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                                                            <?php endif; ?>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&sort=divisi&order=<?= ($sort_by == 'divisi' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?>" 
                                                           class="text-dark text-decoration-none">
                                                            Divisi
                                                            <?php if ($sort_by == 'divisi'): ?>
                                                                <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                                                            <?php endif; ?>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&sort=waktu_mulai&order=<?= ($sort_by == 'waktu_mulai' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?>" 
                                                           class="text-dark text-decoration-none">
                                                            Mulai
                                                            <?php if ($sort_by == 'waktu_mulai'): ?>
                                                                <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                                                            <?php endif; ?>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&sort=waktu_selesai&order=<?= ($sort_by == 'waktu_selesai' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?>" 
                                                           class="text-dark text-decoration-none">
                                                            Selesai
                                                            <?php if ($sort_by == 'waktu_selesai'): ?>
                                                                <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                                                            <?php endif; ?>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&sort=durasi&order=<?= ($sort_by == 'durasi' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?>" 
                                                           class="text-dark text-decoration-none">
                                                            Durasi
                                                            <?php if ($sort_by == 'durasi'): ?>
                                                                <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                                                            <?php endif; ?>
                                                        </a>
                                                    </th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($hari_data['data'] as $r): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($r['nama']) ?></td>
                                                        <td><?= htmlspecialchars($r['divisi'] ?? '-') ?></td>
                                                        <td><?= $r['waktu_mulai'] ? date('H:i:s', strtotime($r['waktu_mulai'])) : '-' ?></td>
                                                        <td><?= $r['waktu_selesai'] ? date('H:i:s', strtotime($r['waktu_selesai'])) : '-' ?></td>
                                                        <td><?= $r['durasi'] ? number_format($r['durasi'] / 60, 1) . ' jam' : ($r['durasi'] ? $r['durasi'] . ' menit' : '-') ?></td>
                                                        <td><?= $r['catatan'] ? htmlspecialchars($r['catatan']) : '-' ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-secondary">
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold">Total Hari Ini:</td>
                                                    <td class="fw-bold"><?= number_format($hari_data['total_durasi'] / 60, 1) ?> jam</td>
                                                    <td colspan="1"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

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
</html>

