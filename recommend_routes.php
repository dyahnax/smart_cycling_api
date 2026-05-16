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
    $routes = [];

    $max_jarak = 0.1; // Menghindari pembagian dengan nol
    $min_jarak = 999999;

    while ($row = mysqli_fetch_assoc($result)) {
        $routes[] = $row;
        $jarak = (float)$row['jarak'];
        if ($jarak > $max_jarak) $max_jarak = $jarak;
        if ($jarak < $min_jarak) $min_jarak = $jarak;
    }

    // 2. Bobot SAW (Jenis Sepeda: 0.7, Tipe Pengguna: 0.3)
    $w_sepeda = 0.7;
    $w_pengguna = 0.3;

    $recommendations = [];

    foreach ($routes as $route) {
        $route_jarak = (float)$route['jarak'];
        $route_medan = $route['kondisi_medan'];

        // --- Skor Kriteria 1: Jenis Sepeda vs Kondisi Medan ---
        $skor_sepeda = 0.5; // Default (setengah cocok)

        if ($jenis_sepeda == 'Road Bike') {
            if ($route_medan == 'Beraspal') $skor_sepeda = 1.0;
        } else if ($jenis_sepeda == 'MTB' || $jenis_sepeda == 'Hybrid') {
            if ($route_medan == 'Campuran') $skor_sepeda = 1.0;
        }

        // --- Skor Kriteria 2: Tipe Pengguna vs Jarak ---
        $skor_pengguna = 0.5; // Default

        if ($tipe_pengguna == 'Profesional') {
            // Profesional lebih suka rute jauh (> 10km)
            if ($route_jarak > 10) $skor_pengguna = 1.0;
        } else {
            // Amatir lebih suka rute dekat (< 10km)
            if ($route_jarak <= 10) $skor_pengguna = 1.0;
        }

        // --- Total Skor SAW ---
        $final_score = ($w_sepeda * $skor_sepeda) + ($w_pengguna * $skor_pengguna);

        $recommendations[] = [
            "id_routes" => $route['id_routes'],
            "nama_rute" => $route['nama_rute'],
            "jarak" => $route_jarak,
            "kondisi_medan" => $route_medan,
            "waktu_dasar" => (int)$route['waktu_dasar'],
            "titik_koordinat" => $route['titik_koordinat'],
            "skor" => round($final_score, 4)
        ];
    }

    // 3. Sorting berdasarkan skor tertinggi
    usort($recommendations, function($a, $b) use ($tipe_pengguna) {
        // Jika skor sama, gunakan Jarak sebagai Tie-breaker
        if ($a['skor'] == $b['skor']) {
            if ($tipe_pengguna == 'Profesional') {
                return $b['jarak'] <=> $a['jarak']; // Jauh di atas
            } else {
                return $a['jarak'] <=> $b['jarak']; // Dekat di atas
            }
        }
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
