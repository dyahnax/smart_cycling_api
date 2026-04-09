<?php
// FILE: geo_helper.php

/**
 * Menghitung jarak antara dua titik koordinat (Haversine Formula)
 * @param float $lat1 Latitude titik A
 * @param float $lon1 Longitude titik A
 * @param float $lat2 Latitude titik B
 * @param float $lon2 Longitude titik B
 * @return float Jarak dalam Kilometer
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Radius bumi dalam KM

    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lonDelta / 2) * sin($lonDelta / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}

/**
 * Menghitung total jarak dari sekumpulan titik koordinat
 * @param array $coordinates Array of ['lat' => x, 'lng' => y]
 * @return float Total jarak dalam KM
 */
function calculateRouteDistance($coordinates) {
    if (count($coordinates) < 2) return 0;

    $totalDistance = 0;
    for ($i = 0; $i < count($coordinates) - 1; $i++) {
        $totalDistance += calculateDistance(
            $coordinates[$i]['lat'], $coordinates[$i]['lng'],
            $coordinates[$i+1]['lat'], $coordinates[$i+1]['lng']
        );
    }
    return round($totalDistance, 2);
}
?>
