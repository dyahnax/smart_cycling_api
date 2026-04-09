<?php
// FILE: process_geojson.php
require 'koneksi.php';

// Path file GeoJSON Anda
$geojson_path = 'c:/Users/acer/Downloads/Rute dari MMH7+952, Darussalam, Jatimulyo, Kec. Jenggawah, Kabupaten Jember, Jawa Timur 68171, Indonesia ke MMH7+952, Darussalam, Jatimulyo, Kec. Jenggawah, Kabupaten Jember, Jawa Timur 68171, Indonesia.geojson';

if (!file_exists($geojson_path)) {
    die("Error: File tidak ditemukan di $geojson_path");
}

$data = json_decode(file_get_contents($geojson_path), true);

if (!$data) {
    die("Error: Gagal membaca format JSON.");
}

// Ambil koordinat dari feature pertama
$raw_coords = $data['features'][0]['geometry']['coordinates'];
$formatted_coords = [];

foreach ($raw_coords as $coord) {
    // GeoJSON itu [lng, lat, alt], kita butuh [lat, lng]
    $formatted_coords[] = [
        'lat' => $coord[1],
        'lng' => $coord[0]
    ];
}

$json_coords = mysqli_real_escape_string($con, json_encode($formatted_coords));

// Masukkan sebagai rute baru atau update yang sudah ada
// Disini saya masukkan sebagai rute baru untuk contoh
$nama_rute = "Rute Jenggawah - Pasar Asem (Loop)";
$jarak = 12.5; // Estimasi, bisa disesuaikan
$medan = "Medan Campuran";
$waktu = 45;

$sql = "INSERT INTO routes (nama_rute, jarak, kondisi_medan, waktu_dasar, titik_koordinat) 
        VALUES ('$nama_rute', '$jarak', '$medan', '$waktu', '$json_coords')";

if (mysqli_query($con, $sql)) {
    echo "BERHASIL: Data rute dan koordinat telah dimasukkan ke database.";
    echo "<br>Jumlah titik: " . count($formatted_coords);
} else {
    echo "GAGAL: " . mysqli_error($con);
}
?>
