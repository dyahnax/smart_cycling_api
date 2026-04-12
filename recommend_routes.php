<?php
// FILE: recommend_routes.php
require 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_jarak = isset($_POST['jarak']) ? (float)$_POST['jarak'] : 0;
    $input_medan = isset($_POST['kondisi_medan']) ? $_POST['kondisi_medan'] : '';

    // 1. Ambil semua rute dari database
    $query = "SELECT * FROM routes";
    $result = mysqli_query($con, $query);
    $routes = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $routes[] = $row;
    }

    // 2. Bobot SAW (Setara: 0.5 untuk Jarak, 0.5 untuk Medan)
    $w_jarak = 0.5;
    $w_medan = 0.5;

    $recommendations = [];

    foreach ($routes as $route) {
        $route_jarak = (float)$route['jarak'];
        $route_medan = $route['kondisi_medan'];

        // --- Perhitungan Kriteria 1: Jarak (Cost/Similarity) ---
        // Mencari seberapa dekat jarak rute dengan input pengguna
        // Semakin kecil selisihnya, semakin besar skornya (max 1.0)
        $diff_jarak = abs($route_jarak - $input_jarak);
        $max_ref = max($route_jarak, $input_jarak, 1);
        $skor_jarak = 1 - ($diff_jarak / $max_ref);
        if ($skor_jarak < 0) $skor_jarak = 0;

        // --- Perhitungan Kriteria 2: Kondisi Medan (Benefit) ---
        $skor_medan = 0;
        if ($route_medan == $input_medan) {
            $skor_medan = 1.0;
        } elseif ($route_medan == 'Medan Campuran' || $input_medan == 'Medan Campuran') {
            $skor_medan = 0.5; // Kecocokan moderat
        } else {
            $skor_medan = 0.2; // Tidak cocok
        }

        // --- Total Skor SAW ---
        $final_score = ($w_jarak * $skor_jarak) + ($w_medan * $skor_medan);

        $recommendations[] = [
            "id" => $route['id'],
            "nama_rute" => $route['nama_rute'],
            "jarak" => $route_jarak,
            "kondisi_medan" => $route_medan,
            "waktu_dasar" => (int)$route['waktu_dasar'],
            "titik_koordinat" => $route['titik_koordinat'],
            "skor" => round($final_score, 4)
        ];
    }

    // 3. Sorting berdasarkan skor tertinggi
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
