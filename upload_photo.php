<?php
// FILE: upload_photo.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_users = $_POST['id_users'] ?? '';

    if(empty($id_users) || !isset($_FILES['photo'])) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        exit();
    }

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
    $filename = uniqid("profile_") . "." . $file_extension;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $url_path = "uploads/" . $filename; 

        $stmt = $con->prepare("UPDATE users SET photo_url=? WHERE id_users=?");
        $stmt->bind_param("si", $url_path, $id_users);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Foto diunggah", "photo_url" => $url_path]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal simpan ke DB"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload file"]);
    }
}
$con->close();
?>
