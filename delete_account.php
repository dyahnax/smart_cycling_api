<?php
// FILE: delete_account.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'] ?? '';

    // Mulai Transaksi
    $con->begin_transaction();

    try {
        // 1. Hapus Riwayat Perjalanan
        $stmt_history = $con->prepare("DELETE FROM trip_history WHERE user_email=?");
        $stmt_history->bind_param("s", $user);
        $stmt_history->execute();
        $stmt_history->close();

        // 2. Hapus User
        $stmt = $con->prepare("DELETE FROM users WHERE username=?");
        $stmt->bind_param("s", $user);
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
