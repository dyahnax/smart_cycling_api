<?php
require 'geo_helper.php';
$file = 'c:/laragon/www/smart_cycling_api/import_queue/done/Jalur Watu Ulo.geojson';
$data = json_decode(file_get_contents($file), true);

$total_dist = 0;
$min_lat = 90; $max_lat = -90;
$min_lng = 180; $max_lng = -180;

foreach ($data['features'] as $index => $feature) {
    if ($feature['geometry']['type'] === 'LineString') {
        $raw_coords = $feature['geometry']['coordinates'];
        $formatted_coords = [];
        foreach ($raw_coords as $coord) {
            $lat = $coord[1];
            $lng = $coord[0];
            $formatted_coords[] = ['lat' => $lat, 'lng' => $lng];
            
            if ($lat < $min_lat) $min_lat = $lat;
            if ($lat > $max_lat) $max_lat = $lat;
            if ($lng < $min_lng) $min_lng = $lng;
            if ($lng > $max_lng) $max_lng = $lng;
        }
        $dist = calculateRouteDistance($formatted_coords);
        echo "Feature #$index (LineString): Points: " . count($formatted_coords) . " | Dist: " . $dist . " KM\n";
        $total_dist += $dist;
    }
}

echo "-----------------------------------\n";
echo "Total Calculated Distance: " . $total_dist . " KM\n";
echo "Rentang Latitude: $min_lat sampai $max_lat\n";
echo "Rentang Longitude: $min_lng sampai $max_lng\n";

// Kalkulasi jarak diagonal bounding box untuk gambaran skala
$diag = calculateDistance($min_lat, $min_lng, $max_lat, $max_lng);
echo "Jarak Diagonal Area: " . round($diag, 2) . " KM\n";
?>
