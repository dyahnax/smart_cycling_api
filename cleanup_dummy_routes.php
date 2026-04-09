<?php
// FILE: cleanup_dummy_routes.php
require 'koneksi.php';

// Menghapus rute dengan ID 1 sampai 48 (karena id 49 adalah rute asli pertama Anda)
$sql = "DELETE FROM routes WHERE id BETWEEN 1 AND 48";

if (mysqli_query($con, $sql)) {
    echo "BERHASIL: Semua data dummy (ID 1-48) telah dihapus.";
    echo "<br>Sekarang tabel Anda hanya berisi rute asli.";
} else {
    echo "GAGAL: " . mysqli_error($con);
}
?>
