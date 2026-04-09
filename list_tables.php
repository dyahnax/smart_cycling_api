<?php
require 'koneksi.php';
$result = mysqli_query($con, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}
echo json_encode($tables);
?>
