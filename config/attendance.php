<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Lokasi Kantor
    |--------------------------------------------------------------------------
    |
    | Koordinat kantor/yayasan digunakan untuk validasi lokasi absensi
    |
    */

    'office_latitude' => env('OFFICE_LATITUDE', -6.200000),
    'office_longitude' => env('OFFICE_LONGITUDE', 106.816667),
    'office_radius' => env('OFFICE_RADIUS', 30), // Dalam meter

    /*
    |--------------------------------------------------------------------------
    | Waktu Kerja
    |--------------------------------------------------------------------------
    */
    'check_in_time' => env('CHECK_IN_TIME', '08:00:00'),
    'check_out_time' => env('CHECK_OUT_TIME', '17:00:00'),
];
