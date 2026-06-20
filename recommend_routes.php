<?php
// FILE: recommend_routes.php
require 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_sepeda = isset($_POST['jenis_sepeda']) ? $_POST['jenis_sepeda'] : '';
    $tipe_pengguna = isset($_POST['tipe_pengguna']) ? $_POST['tipe_pengguna'] : '';

    //Ambil semua rute dari database
    $query = "SELECT * FROM routes";
    $result = mysqli_query($con, $query);
    $all_routes = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $all_routes[] = $row;
    }

    // 1. Filter Rute Berdasarkan Input Pengguna
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

    // 2. Sorting berdasarkan jarak sesuai tipe pengguna
    usort($filtered_routes, function($a, $b) use ($tipe_pengguna) {
        $jarak_a = (float)$a['jarak'];
        $jarak_b = (float)$b['jarak'];
        
        if ($tipe_pengguna == 'Profesional') {
            // Profesional: Jarak terjauh di atas (Descending)
            return $jarak_b <=> $jarak_a;
        } else {
            // Amatir: Jarak terdekat di atas (Ascending)
            return $jarak_a <=> $jarak_b;
        }
    });

    // 3. Scoring (Perhitungan Persentase Kecocokan)
    // Karena sudah diurutkan, nilai Max (untuk Profesional) atau Min (untuk Amatir) 
    // pasti berada di urutan pertama (index 0).
    $referensi_jarak = (float)$filtered_routes[0]['jarak'];

    $recommendations = [];
    foreach ($filtered_routes as $route) {
        $jarak = (float)$route['jarak'];
        
        if ($tipe_pengguna == 'Profesional') {
            // Profesional: Skor = Jarak saat ini / Jarak Maksimal (diambil dari referensi)
            $kecocokan = $jarak / ($referensi_jarak > 0 ? $referensi_jarak : 0.1);
        } else {
            // Amatir: Skor = Jarak Minimal (diambil dari referensi) / Jarak saat ini
            $kecocokan = $referensi_jarak / ($jarak > 0 ? $jarak : 0.1);
        }

        $recommendations[] = [
            "id_routes" => $route['id_routes'],
            "nama_rute" => $route['nama_rute'],
            "jarak" => $jarak,
            "kondisi_medan" => $route['kondisi_medan'],
            "waktu_dasar" => (int)$route['waktu_dasar'],
            "titik_koordinat" => $route['titik_koordinat'],
            "skor" => round($kecocokan, 4) // Mengembalikan nilai persentase agar UI tetap berjalan dengan akurat
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $recommendations
    ]);

} else {
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>
