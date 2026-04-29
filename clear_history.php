<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['user_email'] ?? '';

    if (empty($user_email)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit();
    }

    $stmt = $con->prepare("DELETE FROM trip_history WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);

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
