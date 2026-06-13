<?php
// FILE: recommend_routes.php
require 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_sepeda = isset($_POST['jenis_sepeda']) ? $_POST['jenis_sepeda'] : '';
    $tipe_pengguna = isset($_POST['tipe_pengguna']) ? $_POST['tipe_pengguna'] : '';

    // 1. Ambil semua rute dari database
    $query = "SELECT * FROM routes";
    $result = mysqli_query($con, $query);
    $all_routes = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $all_routes[] = $row;
    }

    // 2. Filter Rute Berdasarkan Input Pengguna
    $filtered_routes = [];
    foreach ($all_routes as $route) {
        $jarak = (float)$route['jarak'];
        $medan = $route['kondisi_medan'];

        // Filter Tipe Pengguna (Jarak)
        if ($tipe_pengguna == 'Profesional' && $jarak <= 10) continue;
        if ($tipe_pengguna != 'Profesional' && $jarak > 10) continue; // Asumsi Amatir

        // Filter Jenis Sepeda (Medan)
        if ($jenis_sepeda == 'Road Bike' && $medan != 'Beraspal') continue;
        if (($jenis_sepeda == 'MTB' || $jenis_sepeda == 'Hybrid') && $medan != 'Campuran') continue;

        $filtered_routes[] = $route;
    }

    // Jika tidak ada rute yang cocok setelah di-filter, langsung kembalikan array kosong
    if (count($filtered_routes) == 0) {
        echo json_encode(["status" => "success", "data" => []]);
        exit;
    }

    // 3. Menentukan Nilai Max dan Min Jarak untuk Menghitung Skor Kecocokan (Akurasi UI)
    $max_jarak = 0.1;
    $min_jarak = 999999;

    foreach ($filtered_routes as $route) {
        $jarak = (float)$route['jarak'];

        if ($jarak > $max_jarak) $max_jarak = $jarak;
        if ($jarak < $min_jarak) $min_jarak = $jarak;
    }

    // 4. Proses Perhitungan Kecocokan & Pembuatan List Rekomendasi
    $recommendations = [];

    foreach ($filtered_routes as $route) {
        $jarak = (float)$route['jarak'];

        // Menghitung persentase kecocokan murni dari jarak (karena medan sudah 100% cocok dari filter)
        if ($tipe_pengguna == 'Profesional') {
            // Profesional suka jarak jauh (jarak maksimal = 100% akurat)
            $kecocokan = $jarak / $max_jarak;
        } else {
            // Amatir suka jarak dekat (jarak minimal = 100% akurat)
            $kecocokan = $min_jarak / ($jarak > 0 ? $jarak : 0.1);
        }

        $recommendations[] = [
            "id_routes" => $route['id_routes'],
            "nama_rute" => $route['nama_rute'],
            "jarak" => $jarak,
            "kondisi_medan" => $route['kondisi_medan'],
            "waktu_dasar" => (int)$route['waktu_dasar'],
            "titik_koordinat" => $route['titik_koordinat'],
            "skor" => round($kecocokan, 4) // Tetap pakai key "skor" agar aplikasi Flutter tidak error
        ];
    }

    // 5. Sorting berdasarkan skor kecocokan tertinggi
    usort($recommendations, function($a, $b) {
        return $b['skor'] <=> $a['skor'];
    });

    echo json_encode([
        "status" => "success",
        "data" => $recommendations
    ]);

} else {
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>
