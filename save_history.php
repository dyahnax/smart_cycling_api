<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['user_email'] ?? '';
    $nama_rute = $_POST['nama_rute'] ?? '';
    $jarak = $_POST['jarak'] ?? '';
    $kondisi_medan = $_POST['kondisi_medan'] ?? '';
    $status = $_POST['status'] ?? '';
    $jarak_tempuh = $_POST['jarak_tempuh'] ?? '';
    $waktu_tempuh = $_POST['waktu_tempuh'] ?? 0;
    $kecepatan_rata_rata = $_POST['kecepatan_rata_rata'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d H:i:s');
    $target_waktu = $_POST['target_waktu'] ?? 0;

    if (empty($user_email) || empty($nama_rute)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit();
    }

    $stmt = $con->prepare("INSERT INTO trip_history (user_email, nama_rute, jarak, kondisi_medan, target_waktu, status, jarak_tempuh, waktu_tempuh, kecepatan_rata_rata, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssisiss", $user_email, $nama_rute, $jarak, $kondisi_medan, $target_waktu, $status, $jarak_tempuh, $waktu_tempuh, $kecepatan_rata_rata, $tanggal);

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
