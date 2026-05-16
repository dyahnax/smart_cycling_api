<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_users = $_POST['id_users'] ?? '';

    if (empty($id_users)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit();
    }

    $stmt = $con->prepare("DELETE FROM trip_history WHERE id_users = ?");
    $stmt->bind_param("i", $id_users);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'History cleared successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $con->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
