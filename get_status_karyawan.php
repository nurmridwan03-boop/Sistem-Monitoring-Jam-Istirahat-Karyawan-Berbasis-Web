<?php
include 'db.php';

// Ambil parameter search dan sort
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'nama';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validasi sort_by untuk keamanan
$allowed_sort = ['nama', 'divisi', 'role', 'status', 'waktu_mulai', 'durasi'];
if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'nama';
}

// Validasi sort_order
$sort_order = strtoupper($sort_order);
if ($sort_order != 'ASC' && $sort_order != 'DESC') {
    $sort_order = 'ASC';
}

// Query dengan search dan sort
$where_clause = '';
if (!empty($search)) {
    $where_clause = "AND (u.nama LIKE '%$search%' 
                     OR u.divisi LIKE '%$search%' 
                     OR u.role LIKE '%$search%'
                     OR u.id LIKE '%$search%')";
}

// Mapping sort_by untuk query
$sort_mapping = [
    'nama' => 'u.nama',
    'divisi' => 'u.divisi',
    'role' => 'u.role',
    'status' => 'i.status',
    'waktu_mulai' => 'i.waktu_mulai',
    'durasi' => 'i.durasi'
];
$sort_column = $sort_mapping[$sort_by] ?? 'u.nama';

$q = mysqli_query($conn, "SELECT u.id AS uid, u.nama, u.role, u.divisi, i.id AS iid, i.waktu_mulai, i.waktu_selesai, i.durasi, i.status 
                          FROM users u 
                          LEFT JOIN istirahat i ON u.id=i.user_id AND i.id = (SELECT id FROM istirahat WHERE user_id=u.id ORDER BY id DESC LIMIT 1) 
                          WHERE 1=1 $where_clause
                          ORDER BY $sort_column $sort_order");

// Cek apakah query berhasil
if (!$q) {
    echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    exit;
}

$num_rows = mysqli_num_rows($q);

if ($num_rows == 0 && !empty($search)) {
    echo '<div class="alert alert-warning">Tidak ada data yang ditemukan untuk pencarian "<strong>'.htmlspecialchars($search).'</strong>".</div>';
} else {
    echo '<div class="mb-2"><small class="text-muted">Menampilkan '.$num_rows.' data';
    if (!empty($search)) {
        echo ' untuk pencarian "<strong>'.htmlspecialchars($search).'</strong>"';
    }
    echo '</small></div>';
}

echo '<div class="table-responsive">';
echo '<table class="table table-bordered table-hover"><thead class="table-header-soft"><tr>';
echo '<th style="cursor:pointer;" onclick="sortTable(\'nama\')" class="text-white">Nama';
if ($sort_by == 'nama') echo ' <i class="bi bi-arrow-'.($sort_order == 'ASC' ? 'up' : 'down').'"></i>';
echo '</th>';
echo '<th style="cursor:pointer;" onclick="sortTable(\'divisi\')" class="text-white">Divisi';
if ($sort_by == 'divisi') echo ' <i class="bi bi-arrow-'.($sort_order == 'ASC' ? 'up' : 'down').'"></i>';
echo '</th>';
echo '<th style="cursor:pointer;" onclick="sortTable(\'role\')" class="text-white">Role';
if ($sort_by == 'role') echo ' <i class="bi bi-arrow-'.($sort_order == 'ASC' ? 'up' : 'down').'"></i>';
echo '</th>';
echo '<th style="cursor:pointer;" onclick="sortTable(\'status\')" class="text-white">Status';
if ($sort_by == 'status') echo ' <i class="bi bi-arrow-'.($sort_order == 'ASC' ? 'up' : 'down').'"></i>';
echo '</th>';
echo '<th class="text-white">Mulai</th>';
echo '<th class="text-white">Selesai</th>';
echo '<th class="text-white">Durasi</th>';
echo '<th class="text-white">Aksi</th>';
echo '</tr></thead><tbody>';

if ($num_rows > 0) {
while($r = mysqli_fetch_assoc($q)){
    $status = $r['status'] ? $r['status'] : 'belum';
    $badge = '<span class="badge bg-secondary status-badge">Belum</span>';
    if ($status == 'sedang') $badge = '<span class="badge bg-warning text-dark status-badge">Sedang Istirahat</span>';
    if ($status == 'selesai') $badge = '<span class="badge bg-success status-badge">Selesai</span>';
    $user_id = intval($r['uid']);
    $divisi = $r['divisi'] ? htmlspecialchars($r['divisi']) : '-';
    $resetBtn = '<button class="btn btn-sm btn-danger reset-btn" data-user-id="'.$user_id.'">Reset</button>';
    echo '<tr id="row-'.$user_id.'"><td>'.htmlspecialchars($r['nama']).'</td><td>'.$divisi.'</td><td>'.htmlspecialchars($r['role']).'</td><td>'.$badge.'</td><td>'.($r['waktu_mulai']?htmlspecialchars($r['waktu_mulai']):'-').'</td><td>'.($r['waktu_selesai']?htmlspecialchars($r['waktu_selesai']):'-').'</td><td>'.($r['durasi']?intval($r['durasi']):'-').'</td><td>'.$resetBtn.'</td></tr>';
}
}
echo '</tbody></table>';
echo '</div>';
?>