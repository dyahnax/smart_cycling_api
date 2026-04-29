<?php
require 'koneksi.php';

$sql = "CREATE TABLE IF NOT EXISTS trip_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    nama_rute VARCHAR(255) NOT NULL,
    jarak VARCHAR(50) NOT NULL,
    kondisi_medan VARCHAR(50) NOT NULL,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL,
    jarak_tempuh VARCHAR(50) NOT NULL,
    waktu_tempuh INT NOT NULL,
    kecepatan_rata_rata VARCHAR(50) NOT NULL
)";

if (mysqli_query($con, $sql)) {
    echo "SUCCESS: Table 'trip_history' created.";
} else {
    echo "ERROR: " . mysqli_error($con);
}
?>
