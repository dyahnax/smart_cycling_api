<?php
require 'koneksi.php';

$sql = "ALTER TABLE trip_history ADD COLUMN target_waktu INT NOT NULL DEFAULT 0 AFTER kondisi_medan";

if (mysqli_query($con, $sql)) {
    echo "SUCCESS: Column 'target_waktu' added to trip_history table.";
} else {
    echo "ERROR: " . mysqli_error($con);
}
?>
