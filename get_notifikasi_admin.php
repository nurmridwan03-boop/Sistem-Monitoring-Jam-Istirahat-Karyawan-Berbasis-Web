<?php
include 'db.php';
$q = mysqli_query($conn, "SELECT n.*, u.nama FROM notifikasi n JOIN users u ON n.id_karyawan=u.id ORDER BY n.waktu DESC LIMIT 50");

// Cek apakah query berhasil
if (!$q) {
    echo '<div class="alert alert-danger m-3">Error: ' . mysqli_error($conn) . '</div>';
    exit;
}

$num_rows = mysqli_num_rows($q);

if ($num_rows == 0) {
    echo '<div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash" style="font-size: 3rem;"></i>
            <p class="mt-3 mb-0">Tidak ada notifikasi</p>
          </div>';
} else {
    echo '<div class="list-group list-group-flush">';
    while($r = mysqli_fetch_assoc($q)){
        $waktu = htmlspecialchars($r['waktu']);
        $nama = htmlspecialchars($r['nama']);
        $pesan = htmlspecialchars($r['pesan']);
        
        echo '<div class="list-group-item border-start-0 border-end-0 border-bottom" style="border-top: 1px solid #e9ecef;">';
        echo '<div class="d-flex align-items-start">';
        echo '<div class="flex-shrink-0 me-3 mt-1">';
        echo '<div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">';
        echo '<i class="bi bi-bell-fill"></i>';
        echo '</div>';
        echo '</div>';
        echo '<div class="flex-grow-1">';
        echo '<div class="fw-semibold text-dark mb-1">'.$pesan.'</div>';
        echo '<div class="d-flex align-items-center text-muted small">';
        echo '<i class="bi bi-clock me-1"></i>';
        echo '<span class="me-3">'.$waktu.'</span>';
        echo '<i class="bi bi-person me-1"></i>';
        echo '<span class="fw-bold text-dark" style="font-size: 1rem;">'.$nama.'</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
?>