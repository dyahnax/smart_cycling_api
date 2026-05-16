<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_users = $_POST['id_users'] ?? '';
    $id_routes = $_POST['id_routes'] ?? '';
    $status = $_POST['status'] ?? '';
    $jarak_tempuh = $_POST['jarak_tempuh'] ?? '';
    $waktu_tempuh = $_POST['waktu_tempuh'] ?? 0;
    $kecepatan_rata_rata = $_POST['kecepatan_rata_rata'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d H:i:s');
    $target_waktu = $_POST['target_waktu'] ?? 0;

    if (empty($id_users) || empty($id_routes)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit();
    }

    $stmt = $con->prepare("INSERT INTO trip_history (id_users, id_routes, target_waktu, status, jarak_tempuh, waktu_tempuh, kecepatan_rata_rata, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssss", $id_users, $id_routes, $target_waktu, $status, $jarak_tempuh, $waktu_tempuh, $kecepatan_rata_rata, $tanggal);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'History saved successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $con->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
