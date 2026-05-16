<?php
// FILE: login.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Cek password (mendukung teks biasa atau password_hash)
        if (password_verify($password, $user['password']) || $password == $user['password']) {
            echo json_encode([
                "status"  => "success",
                "message" => "Login Berhasil",
                "user"    => [
                    "id_users" => $user['id_users'],
                    "nama"     => $user['nama'],
                    "username" => $user['username'],
                    "photo_url" => $user['photo_url']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }
    $stmt->close();
}
$con->close();
?>
