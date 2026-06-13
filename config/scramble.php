<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Route prefix untuk semua endpoint API yang akan didokumentasikan.
     * Scramble hanya akan mendokumentasikan route yang diawali prefix ini.
     */
    'api_path' => 'api',

    /*
     * Base domain untuk API. Kosongkan jika sama dengan app URL.
     */
    'api_domain' => null,

    /*
     * Versi API yang ditampilkan di dokumentasi.
     */
    'version' => '1.0.0',

    /*
     * Info tambahan yang ditampilkan di dokumentasi.
     */
    'info' => [
        'title' => config('app.name') . ' API',
        'description' => 'Dokumentasi API Kelompok 3 - Mahasiswa',
    ],

    /*
     * Filter route mana saja yang akan muncul di dokumentasi Scramble.
     * Route yang return false tidak akan ditampilkan.
     */
    'routes' => function (\Illuminate\Routing\Route $route) {
    // Sembunyikan berdasarkan nama route
    if (str_starts_with($route->getName() ?? '', 'internal.')) {
        return false;
    }

    // Sembunyikan debug-token
    if ($route->getName() === 'debugtoken') {
        return false;
    }

    return str_starts_with($route->uri(), 'api/');
    },

    /*
     * Server yang ditampilkan di dokumentasi.
     * Kosongkan array untuk menggunakan URL aplikasi secara otomatis.
     */
    'servers' => null,

    /*
     * Middleware yang digunakan untuk melindungi halaman dokumentasi.
     * Kosongkan array agar dokumentasi bisa diakses semua orang.
     */
    'middleware' => [
        'web',
        // RestrictedDocsAccess::class, // Aktifkan ini jika ingin docs hanya untuk admin
    ],

    /*
     * Konfigurasi tambahan untuk OpenAPI.
     */
    'extensions' => [],
];