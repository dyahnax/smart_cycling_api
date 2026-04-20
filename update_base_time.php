<?php
// FILE: update_base_time.php
// Script ini sekarang berfungsi sebagai "Sync" untuk sinkronisasi ulang 
// semua waktu_dasar rute berdasarkan trigger database.

require 'koneksi.php';

echo "Memulai sinkronisasi waktu_dasar rute...\n";

// Kita paksa trigger untuk jalan dengan mengosongkan waktu_dasar.
// Trigger UPDATE kita memiliki kondisi khusus: OR NEW.waktu_dasar = 0
$query = "UPDATE routes SET waktu_dasar = 0";

if (mysqli_query($con, $query)) {
    $affected = mysqli_affected_rows($con);
    echo "BERHASIL: Trigger telah dijalankan untuk seluruh rute.\n";
    echo "Jumlah rute yang diproses: $affected\n";
} else {
    echo "GAGAL: " . mysqli_error($con) . "\n";
}

echo "Sinkronisasi Selesai.\n";
?>
