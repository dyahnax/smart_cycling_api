<?php
require 'koneksi.php';
$result = mysqli_query($con, "DESCRIBE trip_history");
$columns = [];
while ($row = mysqli_fetch_assoc($result)) {
    $columns[] = $row;
}
echo json_encode($columns);
?>
