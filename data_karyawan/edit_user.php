<?php
session_start();
include 'db.php';

// Pastikan hanya admin yang bisa edit
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');
$role = $_SESSION['role'];

// Ambil data berdasarkan ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID tidak valid.';
    header('Location: users.php');
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan
if (!$data) {
    $_SESSION['error'] = 'Data tidak ditemukan!';
    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Edit Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bstrep/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/sidebar-style.css">
    <link rel="icon" type="bstrep/image/x-icon" href="../assets/favicon.ico" />
    <style>
        /* Navbar Styling */
        .navbar {
            border-bottom: 3px solid rgba(255,255,255,0.2);
        }
        
        /* Form Card Styling */
        .form-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 20px 20px 0 0;
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
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
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
        
        .btn-secondary-modern {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white !important;
        }
        
        .btn-secondary-modern:hover {
            background: linear-gradient(135deg, #5a6268 0%, #6c757d 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
            color: white !important;
        }
        
        .foto-preview {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .form-header {
                padding: 1rem;
            }
            
            .form-header .bg-white {
                width: 60px !important;
                height: 60px !important;
            }
            
            .form-header .bg-white i {
                font-size: 2rem !important;
            }
            
            .form-header h4 {
                font-size: 1.1rem;
            }
            
            .form-header p {
                font-size: 0.85rem;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            .form-label-modern {
                font-size: 0.9rem;
            }
            
            .btn-primary-modern, .btn-secondary-modern {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .d-md-flex {
                flex-direction: column !important;
            }
            
            .foto-preview {
                max-width: 120px;
                max-height: 120px;
            }
        }
    </style>
</head>

<body>
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
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Data Karyawan
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
                    <?php if (isset($_SESSION['error'])): 
                        $error_msg = $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error!</strong> <?= htmlspecialchars($error_msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <script>
                            alert("<?= addslashes(htmlspecialchars($error_msg)) ?>");
                        </script>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Berhasil!</strong> <?= htmlspecialchars($_SESSION['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="card form-card">
                                <div class="form-header text-center">
                                    <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-pencil-square" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4 class="mb-0">Edit Data Karyawan</h4>
                                    <p class="mb-0 mt-2" style="opacity: 0.9;">Ubah informasi karyawan di bawah ini</p>
                                </div>

                                <div class="card-body p-4">
                                    <form method="POST" action="update_user.php" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']); ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-person"></i>Nama
                                                </label>
                                                <input type="text" name="nama" class="form-control form-control-modern" value="<?= htmlspecialchars($data['nama']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-calendar"></i>Umur
                                                </label>
                                                <input type="number" name="umur" class="form-control form-control-modern" value="<?= htmlspecialchars($data['umur']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-gender-ambiguous"></i>Jenis Kelamin
                                                </label>
                                                <select name="jenis_kelamin" class="form-select form-select-modern" required>
                                                    <option value="Laki-laki" <?= ($data['jenis_kelamin']=='Laki-laki')?'selected':''; ?>>Laki-laki</option>
                                                    <option value="Perempuan" <?= ($data['jenis_kelamin']=='Perempuan')?'selected':''; ?>>Perempuan</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-building"></i>Divisi
                                                </label>
                                                <input type="text" name="divisi" class="form-control form-control-modern" value="<?= htmlspecialchars($data['divisi']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label-modern">
                                                <i class="bi bi-geo-alt"></i>Alamat
                                            </label>
                                            <textarea name="alamat" class="form-control form-control-modern" rows="3" required><?= htmlspecialchars($data['alamat']); ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-envelope"></i>Email
                                                </label>
                                                <input type="email" name="email" class="form-control form-control-modern" value="<?= htmlspecialchars($data['email']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-telephone"></i>Nomor HP
                                                </label>
                                                <input type="text" name="nomerHP" class="form-control form-control-modern" value="<?= htmlspecialchars($data['nomerHP'] ?? ''); ?>" placeholder="Contoh: 081234567890">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-shield-check"></i>Role
                                                </label>
                                                <select name="role" class="form-select form-select-modern" required>
                                                    <option value="admin" <?= ($data['role']=='admin' || $data['role']=='Admin')?'selected':''; ?>>Admin</option>
                                                    <option value="karyawan" <?= ($data['role']=='karyawan' || $data['role']=='Karyawan')?'selected':''; ?>>Karyawan</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-image"></i>Foto Saat Ini
                                                </label>
                                                <div>
                                                    <?php if (!empty($data['foto'])) { ?>
                                                        <img src="uploads/<?= htmlspecialchars($data['foto']); ?>" class="foto-preview mb-2" alt="Foto saat ini">
                                                    <?php } else { ?>
                                                        <p class="text-muted mb-0"><i class="bi bi-image me-1"></i>Belum ada foto</p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label-modern">
                                                <i class="bi bi-upload"></i>Ganti Foto (Opsional)
                                            </label>
                                            <input type="file" name="foto" accept="image/*" class="form-control form-control-modern">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Kosongkan jika tidak ingin mengganti foto
                                            </small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-person-badge"></i>Username
                                                </label>
                                                <input type="text" name="username" class="form-control form-control-modern" value="<?= htmlspecialchars($data['username']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label-modern">
                                                    <i class="bi bi-lock"></i>Password Baru (Opsional)
                                                </label>
                                                <input type="text" name="password" class="form-control form-control-modern" placeholder="Kosongkan jika tidak diganti">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Kosongkan jika tidak ingin mengubah password
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                            <a href="users.php" class="btn btn-secondary-modern">
                                                <i class="bi bi-arrow-left me-2"></i>Kembali
                                            </a>
                                            <button type="submit" class="btn btn-primary-modern">
                                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                                            </button>
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
        <script src="../bstrep/js/scripts.js"></script>
        
        <!-- Firebase SDK -->
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js"></script>
        <!-- FCM Setup Script - Notifikasi akan muncul di semua halaman -->
        <script src="../assets/fcm-setup.js"></script>
    </body>
</html>
