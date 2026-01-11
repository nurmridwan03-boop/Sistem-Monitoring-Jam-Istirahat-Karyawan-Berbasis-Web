<?php
include 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="data_istirahat.csv"');
$out = fopen('php://output','w');
fputcsv($out, ['Nama','Mulai','Selesai','Durasi_menit','Catatan','Otomatis']);
$q = mysqli_query($conn, "SELECT u.nama, i.waktu_mulai, i.waktu_selesai, i.durasi, i.catatan, i.otomatis_selesai FROM istirahat i JOIN users u ON i.user_id=u.id ORDER BY i.id DESC");
while($r = mysqli_fetch_assoc($q)){
    $r['otomatis_selesai'] = $r['otomatis_selesai'] ? 'Ya' : 'Tidak';
    fputcsv($out, $r);
}
fclose($out);
exit;
?>