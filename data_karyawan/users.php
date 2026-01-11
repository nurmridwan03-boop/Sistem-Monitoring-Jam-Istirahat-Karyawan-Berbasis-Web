<?php
session_start();
include 'db.php';

// **Cek apakah login sebagai admin**
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');

// Ambil parameter search dan sort
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi sort_by untuk keamanan
$allowed_sort = ['id', 'nama', 'umur', 'divisi', 'jenis_kelamin', 'email'];
if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'id';
}

// Validasi sort_order
$sort_order = strtoupper($sort_order);
if ($sort_order != 'ASC' && $sort_order != 'DESC') {
    $sort_order = 'DESC';
}

// Query dengan search dan sort
$where_clause = '';
if (!empty($search)) {
    $where_clause = "WHERE nama LIKE '%$search%' 
                     OR id LIKE '%$search%' 
                     OR divisi LIKE '%$search%' 
                     OR email LIKE '%$search%'
                     OR nomerHP LIKE '%$search%'";
}

$query = "SELECT * FROM users $where_clause ORDER BY $sort_by $sort_order";
$res = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title>Data Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../bstrep/css/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/sidebar-style.css">
<link rel="icon" type="bstrep/image/x-icon" href="../assets/favicon.ico" />
<style>
    table {
        width: 100%;
        table-layout: auto !important;
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
    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }
    
    .btn-primary-modern:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'karyawan.php' ? 'active' : '' ?>" href="../karyawan.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'status_karyawan.php' ? 'active' : '' ?>" href="../status_karyawan.php"><i class="bi bi-people"></i> Status Karyawan</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>" href="../profile.php"><i class="bi bi-person-circle"></i> Profil</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'riwayat_istirahat.php' ? 'active' : '' ?>" href="../riwayat_istirahat.php"><i class="bi bi-clock-history"></i> Riwayat Istirahat</a>
                    <?php } ?>

                    <?php if ($role == 'admin') { ?>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>" href="../profile.php"><i class="bi bi-person-circle"></i> Profil</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>" href="users.php"><i class="bi bi-people-fill"></i> Data Karyawan</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'admin_break.php' ? 'active' : '' ?>" href="../admin_break.php"><i class="bi bi-cup-hot"></i> Mulai Istirahat</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'riwayat_istirahat.php' ? 'active' : '' ?>" href="../riwayat_istirahat.php"><i class="bi bi-clock-history"></i> Riwayat Istirahat Saya</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'riwayat_istirahat_admin.php' ? 'active' : '' ?>" href="../riwayat_istirahat_admin.php"><i class="bi bi-journal-text"></i> Riwayat Semua Karyawan</a>
                    <a class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) == 'notifikasi.php' ? 'active' : '' ?>" href="../notifikasi.php"><i class="bi bi-bell-fill"></i> Notifikasi</a>
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
                <i class="bi bi-people-fill me-2"></i>
                Data Karyawan
            </span>
        </div>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle me-1"></i>
                <?= $nama ?>
            </span>
            <a href="../process.php?logout" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>
<div class="container-fluid">
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-people-fill me-2"></i>Daftar Semua Karyawan</h4>
        <div>
            <a href="export_excel.php" class="btn btn-success btn-sm me-2">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="export_csv.php" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
            </a>
            <a href="create_user.php" class="btn btn-primary-modern">
                <i class="bi bi-plus-circle me-2"></i>Tambah Data
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error!</strong> <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            // Tampilkan popup error
            alert("<?= addslashes(htmlspecialchars($_SESSION['error'])) ?>");
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Berhasil!</strong> <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Search dan Sort -->
    <div class="card card-modern shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="users.php" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Cari Karyawan</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan nama, ID, divisi, email, atau nomor HP..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="users.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Urutkan Berdasarkan</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="id" <?= ($sort_by == 'id') ? 'selected' : '' ?>>ID</option>
                        <option value="nama" <?= ($sort_by == 'nama') ? 'selected' : '' ?>>Nama</option>
                        <option value="umur" <?= ($sort_by == 'umur') ? 'selected' : '' ?>>Umur</option>
                        <option value="divisi" <?= ($sort_by == 'divisi') ? 'selected' : '' ?>>Divisi</option>
                        <option value="jenis_kelamin" <?= ($sort_by == 'jenis_kelamin') ? 'selected' : '' ?>>Jenis Kelamin</option>
                        <option value="email" <?= ($sort_by == 'email') ? 'selected' : '' ?>>Email</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Urutan</label>
                    <select name="order" class="form-select" onchange="this.form.submit()">
                        <option value="ASC" <?= ($sort_order == 'ASC') ? 'selected' : '' ?>>A-Z / Kecil ke Besar</option>
                        <option value="DESC" <?= ($sort_order == 'DESC') ? 'selected' : '' ?>>Z-A / Besar ke Kecil</option>
                    </select>
                </div>
                <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card card-modern shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($search) && mysqli_num_rows($res) == 0): ?>
                <div class="alert alert-info m-3">Tidak ada data karyawan.</div>
            <?php elseif (!empty($search) && mysqli_num_rows($res) == 0): ?>
                <div class="alert alert-warning m-3">Tidak ada data yang ditemukan untuk "<strong><?= htmlspecialchars($search) ?></strong>".</div>
            <?php else: ?>
            <div class="mb-2 p-3 pb-0">
                <small class="text-muted">
                    Menampilkan <?= mysqli_num_rows($res) ?> data
                    <?php if (!empty($search)): ?>
                        untuk pencarian "<strong><?= htmlspecialchars($search) ?></strong>"
                    <?php endif; ?>
                </small>
            </div>
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover align-middle text-center mb-0">
    <thead class="table-header-soft">
        <tr>
            <th>No</th>
            <th class="text-white">
                <a href="?sort=id&order=<?= ($sort_by == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                   class="text-white text-decoration-none">
                    ID 
                    <?php if ($sort_by == 'id'): ?>
                        <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                    <?php endif; ?>
                </a>
            </th>
            <th class="text-white">
                <a href="?sort=nama&order=<?= ($sort_by == 'nama' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                   class="text-white text-decoration-none">
                    Nama
                    <?php if ($sort_by == 'nama'): ?>
                        <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                    <?php endif; ?>
                </a>
            </th>
            <th class="text-white">Jenis Kelamin</th>
            <th class="text-white">
                <a href="?sort=umur&order=<?= ($sort_by == 'umur' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                   class="text-white text-decoration-none">
                    Umur
                    <?php if ($sort_by == 'umur'): ?>
                        <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                    <?php endif; ?>
                </a>
            </th>
            <th class="text-white">
                <a href="?sort=divisi&order=<?= ($sort_by == 'divisi' && $sort_order == 'ASC') ? 'DESC' : 'ASC' ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                   class="text-white text-decoration-none">
                    Divisi
                    <?php if ($sort_by == 'divisi'): ?>
                        <i class="bi bi-arrow-<?= $sort_order == 'ASC' ? 'up' : 'down' ?>"></i>
                    <?php endif; ?>
                </a>
            </th>
            <th class="text-white">Aksi</th>
        </tr>
    </thead>

    <tbody>
    <?php 
    $no = 1;
    mysqli_data_seek($res, 0);
    while ($u = mysqli_fetch_assoc($res)): 
    ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $u['id'] ?></td>
            <td><?= $u['nama'] ?></td>
            <td><?= $u['jenis_kelamin'] ?></td>
            <td><?= $u['umur'] ?></td> <!-- DITAMBAHKAN -->
            <td><?= $u['divisi'] ?></td>

            <td class="text-nowrap">

                <button class="btn btn-info btn-sm text-white"
                    data-bs-toggle="modal"
                    data-bs-target="#detailModal<?= $u['id'] ?>">
                    <i class="bi bi-eye me-1"></i>Detail
                </button>

                <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>

                <a href="delete_user.php?id=<?= $u['id'] ?>"
                   onclick="return confirm('Yakin ingin menghapus data ini?')"
                   class="btn btn-sm btn-danger">
                   <i class="bi bi-trash me-1"></i>Hapus
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>

            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<?php
// Reset pointer untuk modal detail
mysqli_data_seek($res, 0);
while ($row = mysqli_fetch_assoc($res)):
?>

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Detail Karyawan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="row">

          <!-- FOTO -->
          <div class="col-md-4 text-center mb-3">
              <img src="uploads/<?= $row['foto'] ?>" 
                   class="img-thumbnail rounded" 
                   style="width: 150px; height:150px; object-fit:cover;">
          </div>

          <div class="col-md-8">

            <div class="row mb-2">
                <div class="col-5 fw-bold">ID</div>
                <div class="col-7"><?= $row['id'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Nama</div>
                <div class="col-7"><?= $row['nama'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Jenis Kelamin</div>
                <div class="col-7"><?= $row['jenis_kelamin'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Umur</div>
                <div class="col-7"><?= $row['umur'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Divisi</div>
                <div class="col-7"><?= $row['divisi'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Alamat</div>
                <div class="col-7"><?= $row['alamat'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Email</div>
                <div class="col-7"><?= $row['email'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Nomor HP</div>
                <div class="col-7"><?= $row['nomerHP'] ?? '-' ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Username</div>
                <div class="col-7"><?= $row['username'] ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-5 fw-bold">Role</div>
                <div class="col-7"><?= $row['role'] ?></div>
            </div>

          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

<?php endwhile; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="../bstrep/js/scripts.js"></script>
        
        <!-- Firebase SDK -->
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js"></script>
        <!-- FCM Setup Script - Notifikasi akan muncul di semua halaman -->
        <script src="../assets/fcm-setup.js"></script>

</body>
</html>
