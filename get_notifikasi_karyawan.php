<?php
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
$uid = intval($_SESSION['user_id']);
$res = mysqli_query($conn, "SELECT * FROM notifikasi WHERE id_karyawan=$uid AND status_baca=0 ORDER BY waktu DESC");
$rows = [];
if ($res) {
    while($r = mysqli_fetch_assoc($res)) $rows[] = $r;
}
if (count($rows)>0) mysqli_query($conn, "UPDATE notifikasi SET status_baca=1 WHERE id_karyawan=$uid AND status_baca=0");
echo json_encode($rows);
?>