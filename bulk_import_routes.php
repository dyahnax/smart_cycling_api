<?php
// FILE: bulk_import_routes.php
require 'koneksi.php';
require 'geo_helper.php';

$queue_dir = 'import_queue/';
$done_dir = 'import_queue/done/';

// Pastikan folder ada
if (!is_dir($queue_dir)) mkdir($queue_dir, 0777, true);
if (!is_dir($done_dir)) mkdir($done_dir, 0777, true);

// Mencari semua file .geojson di folder queue
$files = glob($queue_dir . "*.geojson");

if (empty($files)) {
    echo "Tidak ada file .geojson baru di folder $queue_dir";
    exit;
}

$success_count = 0;
$error_count = 0;

foreach ($files as $file_path) {
    $filename = basename($file_path);
    $data = json_decode(file_get_contents($file_path), true);

    if (!$data) {
        echo "Gagal membaca file: $filename <br>";
        $error_count++;
        continue;
    }

    // Ekstrak koordinat dari LineString (feature pertama)
    $raw_coords = $data['features'][0]['geometry']['coordinates'];
    $formatted_coords = [];
    foreach ($raw_coords as $coord) {
        $formatted_coords[] = [
            'lat' => $coord[1],
            'lng' => $coord[0]
        ];
    }

    // 1. Hitung Jarak Otomatis
    $jarak_km = calculateRouteDistance($formatted_coords);
    
    // 2. Siapkan data database
    $json_coords = mysqli_real_escape_string($con, json_encode($formatted_coords));
    
    // Nama rute diambil dari nama file (hilangkan .geojson)
    $nama_rute = str_replace(['.geojson', '_'], [' ', ' '], $filename);
    $nama_rute = ucwords(trim($nama_rute));

    // Default values sesuai keinginan user
    $kondisi_medan = "Belum Diobservasi"; 
    $waktu_dasar = 0; // Menyusul

    $sql = "INSERT INTO routes (nama_rute, jarak, kondisi_medan, waktu_dasar, titik_koordinat) 
            VALUES ('$nama_rute', '$jarak_km', '$kondisi_medan', '$waktu_dasar', '$json_coords')";

    if (mysqli_query($con, $sql)) {
        // Pindahkan file ke folder 'done' agar tidak di-import ulang
        rename($file_path, $done_dir . $filename);
        echo "Berhasil Import: <b>$nama_rute</b> ($jarak_km KM) <br>";
        $success_count++;
    } else {
        echo "Gagal Import $filename: " . mysqli_error($con) . "<br>";
        $error_count++;
    }
}

echo "<hr>";
echo "Proses Selesai! <br>";
echo "Berhasil: $success_count rute <br>";
echo "Gagal: $error_count rute";
?>
