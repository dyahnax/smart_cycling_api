<?php
// FILE: delete_account.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'] ?? '';

    $stmt = $con->prepare("DELETE FROM users WHERE username=?");
    $stmt->bind_param("s", $user);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Akun dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal hapus"]);
    }
    $stmt->close();
}
$con->close();
?>
