<?php
require 'koneksi.php';
$sql = "ALTER TABLE routes ADD COLUMN titik_koordinat LONGTEXT AFTER waktu_dasar";
if (mysqli_query($con, $sql)) {
    echo "SUCCESS: Column 'titik_koordinat' added.";
} else {
    echo "ERROR: " . mysqli_error($con);
}
?>
