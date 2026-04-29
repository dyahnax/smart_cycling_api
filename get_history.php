<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_email = $_GET['user_email'] ?? '';

    if (empty($user_email)) {
        echo json_encode([]);
        exit();
    }

    $stmt = $con->prepare("SELECT * FROM trip_history WHERE user_email = ? ORDER BY tanggal DESC");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'user_email' => $row['user_email'],
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
