<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Store Location
    |--------------------------------------------------------------------------
    |
    | Koordinat lokasi toko untuk perhitungan ongkir berdasarkan jarak.
    |
    */
    'store_lat' => env('STORE_LAT', -7.8),
    'store_lng' => env('STORE_LNG', 110.4),

    /*
    |--------------------------------------------------------------------------
    | Price Per KM
    |--------------------------------------------------------------------------
    |
    | Harga per kilometer untuk perhitungan ongkir pengiriman.
    |
    */
    'price_per_km' => env('PRICE_PER_KM', 3000),
];
