<?php
require 'koneksi.php';
// Test UPDATE trigger
mysqli_query($con, "UPDATE routes SET kondisi_medan = 'Medan Berkerikil' WHERE id = 58");

$res = mysqli_query($con, 'SELECT id, nama_rute, jarak, kondisi_medan, waktu_dasar FROM routes WHERE id = 58');
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Rute: {$row['nama_rute']} | Jarak: {$row['jarak']} | Medan: {$row['kondisi_medan']} | Waktu: {$row['waktu_dasar']} menit\n";
}
?>
