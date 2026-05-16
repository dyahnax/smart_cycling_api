<?php
// FILE: delete_account.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_users = $_POST['id_users'] ?? '';

    if (empty($id_users)) {
        echo json_encode(["status" => "error", "message" => "ID User tidak ditemukan"]);
        exit();
    }

    // Mulai Transaksi
    $con->begin_transaction();

    try {
        // 1. Hapus Riwayat Perjalanan (Otomatis terhapus jika pakai FK CASCADE, tapi eksplisit juga boleh)
        $stmt_history = $con->prepare("DELETE FROM trip_history WHERE id_users=?");
        $stmt_history->bind_param("i", $id_users);
        $stmt_history->execute();
        $stmt_history->close();

        // 2. Hapus User
        $stmt = $con->prepare("DELETE FROM users WHERE id_users=?");
        $stmt->bind_param("i", $id_users);
        $stmt->execute();
        $stmt->close();

        $con->commit();
        echo json_encode(["status" => "success", "message" => "Akun dan riwayat berhasil dihapus"]);
    } catch (Exception $e) {
        $con->rollback();
        echo json_encode(["status" => "error", "message" => "Gagal menghapus akun: " . $e->getMessage()]);
    }
}
$con->close();
?>
