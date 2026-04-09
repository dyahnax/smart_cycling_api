<?php
// FILE: register.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = $_POST['nama'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Gunakan password_verify nanti, simpan hash agar aman
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $cek = $con->prepare("SELECT username FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    
    if ($cek->get_result()->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username sudah digunakan!"]);
    } else {
        $stmt = $con->prepare("INSERT INTO users (nama, username, password, photo_url) VALUES (?, ?, ?, ?)");
        $empty_photo = "";
        $stmt->bind_param("ssss", $nama, $username, $password_hash, $empty_photo);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registrasi berhasil"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal: " . $con->error]);
        }
        $stmt->close();
    }
    $cek->close();
}
$con->close();
?>
