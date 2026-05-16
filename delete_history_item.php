<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing ID parameter']);
        exit();
    }

    $stmt = $con->prepare("DELETE FROM trip_history WHERE id_trip_history = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'History item deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $con->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
