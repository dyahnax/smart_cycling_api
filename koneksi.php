<?php
// FILE: koneksi.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$user = "root";
$pass = "putritrisula14"; 
$db   = "db_smart_cycling_project"; 

$con = mysqli_connect($host, $user, $pass, $db);

if (!$con) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>
