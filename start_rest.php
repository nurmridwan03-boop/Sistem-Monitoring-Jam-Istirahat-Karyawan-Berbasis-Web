<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(["status"=>"error", "msg"=>"Not logged in"]));
}

$user_id = $_SESSION['user_id'];
$durasi = intval($_GET['durasi']);

$start = date("Y-m-d H:i:s");
$end   = date("Y-m-d H:i:s", strtotime("+$durasi minutes"));

$sql = "INSERT INTO istirahat (user_id, start_time, end_time, duration, status) 
        VALUES ($user_id, '$start', '$end', $durasi, 'ongoing')";

if ($conn->query($sql)) {
    exit(json_encode(["status"=>"success"]));
} else {
    exit(json_encode(["status"=>"error","msg"=>$conn->error]));
}
