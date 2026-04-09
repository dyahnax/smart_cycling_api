<?php
require 'koneksi.php';
$result = mysqli_query($con, "DESCRIBE users");
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
echo json_encode($rows);
?>
