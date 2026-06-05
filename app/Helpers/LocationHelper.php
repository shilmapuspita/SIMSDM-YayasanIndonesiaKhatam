<?php

namespace App\Helpers;

/**
 * LocationHelper - Helper untuk kalkulasi jarak lokasi
 * Menggunakan formula Haversine untuk menghitung jarak antara dua koordinat
 */
class LocationHelper
{
    /**
     * Radius Bumi dalam meter
     */
    const EARTH_RADIUS = 6371000;

    /**
     * Hitung jarak antara dua titik koordinat menggunakan formula Haversine
     *
     * @param float $lat1 Latitude titik pertama
     * @param float $lon1 Longitude titik pertama
     * @param float $lat2 Latitude titik kedua
     * @param float $lon2 Longitude titik kedua
     * @return float Jarak dalam meter
     */
    public static function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Konversi derajat ke radian
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Beda koordinat
        $dlat = $lat2Rad - $lat1Rad;
        $dlon = $lon2Rad - $lon1Rad;

        // Formula Haversine
        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Jarak dalam meter
        return self::EARTH_RADIUS * $c;
    }

    /**
     * Cek apakah user berada dalam radius kantor
     *
     * @param float $userLat Latitude user
     * @param float $userLon Longitude user
     * @param float $officeLat Latitude kantor
     * @param float $officeLon Longitude kantor
     * @param int $radiusMeters Radius dalam meter (default: dari config)
     * @return bool
     */
    public static function isWithinOfficeRadius(
        float $userLat,
        float $userLon,
        float $officeLat,
        float $officeLon,
        int $radiusMeters = null
    ): bool {
        if ($radiusMeters === null) {
            $radiusMeters = (int) config('attendance.office_radius', 30);
        }

        $distance = self::haversineDistance($userLat, $userLon, $officeLat, $officeLon);

        return $distance <= $radiusMeters;
    }

    /**
     * Ambil jarak dari user ke kantor
     *
     * @param float $userLat Latitude user
     * @param float $userLon Longitude user
     * @return float Jarak dalam meter
     */
    public static function getDistanceToOffice(float $userLat, float $userLon): float
    {
        $officeLat = (float) config('attendance.office_latitude');
        $officeLon = (float) config('attendance.office_longitude');

        return self::haversineDistance($userLat, $userLon, $officeLat, $officeLon);
    }

    /**
     * Validasi koordinat
     *
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return bool
     */
    public static function isValidCoordinate(float $lat, float $lon): bool
    {
        return $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }
}
