<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_users = $_GET['id_users'] ?? '';

    if (empty($id_users)) {
        echo json_encode([]);
        exit();
    }

    $sql = "SELECT th.*, r.nama_rute, r.jarak, r.kondisi_medan 
            FROM trip_history th 
            JOIN routes r ON th.id_routes = r.id_routes 
            WHERE th.id_users = ? 
            ORDER BY th.tanggal DESC";
            
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_users);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'id' => $row['id_trip_history'],
            'id_users' => $row['id_users'],
            'id_routes' => $row['id_routes'],
            'nama_rute' => $row['nama_rute'],
            'jarak' => $row['jarak'],
            'kondisi_medan' => $row['kondisi_medan'],
            'target_waktu' => (int)$row['target_waktu'],
            'tanggal' => $row['tanggal'],
            'status' => $row['status'],
            'jarak_tempuh' => $row['jarak_tempuh'],
            'waktu_tempuh' => (int)$row['waktu_tempuh'],
            'kecepatan_rata_rata' => $row['kecepatan_rata_rata'],
        ];
    }
    echo json_encode($history);
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
