<?php
// FILE: edit_profile.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_username = $_POST['old_username'] ?? ''; 
    $new_nama     = $_POST['nama'] ?? '';
    $new_username = $_POST['username'] ?? '';
    $new_password = $_POST['password'] ?? '';

    if(empty($old_username)) {
        echo json_encode(["status" => "error", "message" => "Identitas pengguna tidak ditemukan"]);
        exit();
    }

    $updates = [];
    $params = [];
    $types = "";

    // Update 'nama' jika diisi
    if(!empty($new_nama)) {
        $updates[] = "nama=?";
        $params[] = $new_nama;
        $types .= "s";
    }

    // Update 'username' jika diisi
    if(!empty($new_username)) {
        $updates[] = "username=?";
        $params[] = $new_username;
        $types .= "s";
    }

    // Update 'password' jika diisi
    if(!empty($new_password)) {
        $updates[] = "password=?";
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        $types .= "s";
    }

    if(empty($updates)) {
        echo json_encode(["status" => "success", "message" => "Tidak ada data yang diubah"]);
        exit();
    }

    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE username=?";
    $params[] = $old_username;
    $types .= "s";

    $stmt = $con->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profil berhasil diperbarui"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal: " . $con->error]);
    }
    $stmt->close();
}
$con->close();
?>