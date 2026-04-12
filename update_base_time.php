<?php
// FILE: update_base_time.php
require 'koneksi.php';

echo "Starting base time calculation...\n";

// Define average speeds (km/h)
$speeds = [
    'Medan Beraspal' => 26,
    'Medan Berkerikil' => 17.5,
    'Medan Campuran' => 21
];

$query = "SELECT id, jarak, kondisi_medan FROM routes";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error fetching routes: " . mysqli_error($con) . "\n");
}

$updated_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $jarak = (float)$row['jarak'];
    $medan = trim($row['kondisi_medan']);
    
    // Default speed if not found
    $speed = 21; // Default to Campuran if unknown
    
    if (isset($speeds[$medan])) {
        $speed = $speeds[$medan];
    } else {
        // Fallback search if exact match fails
        foreach ($speeds as $key => $val) {
            if (stripos($medan, $key) !== false) {
                $speed = $val;
                break;
            }
        }
    }
    
    // Calculate time in minutes: (Distance / Speed) * 60
    $waktu_dasar = round(($jarak / $speed) * 60);
    
    $update_query = "UPDATE routes SET waktu_dasar = $waktu_dasar WHERE id = $id";
    if (mysqli_query($con, $update_query)) {
        echo "Updated Route ID $id: Distance $jarak km, Terrain '$medan' -> Speed $speed km/h -> Time $waktu_dasar mins\n";
        $updated_count++;
    } else {
        echo "Failed to update Route ID $id: " . mysqli_error($con) . "\n";
    }
}

echo "Finished! Total routes updated: $updated_count\n";
?>
