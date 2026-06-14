<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\BiodataController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\OrangTuaWaliController;
use App\Http\Controllers\Api\SekolahController;
use App\Http\Controllers\Api\KrsController;

Route::get('/debug-token', function (Illuminate\Http\Request $request) {
    $authHeader = $request->header('Authorization');
    $token      = substr($authHeader, 7);
    $parts      = explode('.', $token);
    $secret     = env('JWT_SECRET');

    $headerPayload = $parts[0] . '.' . $parts[1];
    $expectedSig   = rtrim(
        strtr(base64_encode(hash_hmac('sha256', $headerPayload, $secret, true)), '+/', '-_'),
        '='
    );

    return response()->json([
        'secret_length'  => strlen($secret),
        'secret_preview' => substr($secret, 0, 10) . '...',
        'expected_sig'   => $expectedSig,
        'actual_sig'     => $parts[2],
        'match'          => hash_equals($expectedSig, $parts[2]),
    ]);
});

Route::get('/internal/mahasiswa',       [MahasiswaController::class, 'internalIndex']);
Route::get('/internal/mahasiswa/{nim}', [MahasiswaController::class, 'internalShow']);
Route::middleware('jwt')->group(function () {

    // MAHASISWA
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])
        ->middleware('jwt:admin-mahasiswa,super-admin,admin-keuangan');

    Route::post('/mahasiswa', [MahasiswaController::class, 'store'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    Route::get('/mahasiswa/{nim}',    [MahasiswaController::class, 'show']);
    Route::put('/mahasiswa/{nim}',    [MahasiswaController::class, 'update']);
    Route::delete('/mahasiswa/{nim}', [MahasiswaController::class, 'destroy'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    // BIODATA
    Route::get('/mahasiswa/{nim}/biodata',    [BiodataController::class, 'show']);
    Route::post('/mahasiswa/{nim}/biodata',   [BiodataController::class, 'store'])
        ->middleware('jwt:admin-mahasiswa,super-admin');
    Route::put('/mahasiswa/{nim}/biodata',    [BiodataController::class, 'update']);
    Route::delete('/mahasiswa/{nim}/biodata', [BiodataController::class, 'destroy'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    // ALAMAT
    Route::get('/mahasiswa/{nim}/alamat',    [AlamatController::class, 'show']);
    Route::post('/mahasiswa/{nim}/alamat',   [AlamatController::class, 'store'])
        ->middleware('jwt:admin-mahasiswa,super-admin');
    Route::put('/mahasiswa/{nim}/alamat',    [AlamatController::class, 'update']);
    Route::delete('/mahasiswa/{nim}/alamat', [AlamatController::class, 'destroy'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    // ORANG TUA & WALI
    Route::get('/mahasiswa/{nim}/ortu',              [OrangTuaWaliController::class, 'index']);
    Route::post('/mahasiswa/{nim}/ortu',             [OrangTuaWaliController::class, 'store'])
        ->middleware('jwt:admin-mahasiswa,super-admin');
    Route::put('/mahasiswa/{nim}/ortu/{id_ortu}',    [OrangTuaWaliController::class, 'update']);
    Route::delete('/mahasiswa/{nim}/ortu/{id_ortu}', [OrangTuaWaliController::class, 'destroy'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    // SEKOLAH
    Route::get('/mahasiswa/{nim}/sekolah',    [SekolahController::class, 'show']);
    Route::post('/mahasiswa/{nim}/sekolah',   [SekolahController::class, 'store'])
        ->middleware('jwt:admin-mahasiswa,super-admin');
    Route::put('/mahasiswa/{nim}/sekolah',    [SekolahController::class, 'update']);
    Route::delete('/mahasiswa/{nim}/sekolah', [SekolahController::class, 'destroy'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    // KRS
    Route::get('/mahasiswa/{nim}/krs',             [KrsController::class, 'index']);
    Route::post('/mahasiswa/{nim}/krs',            [KrsController::class, 'store']);
    Route::get('/mahasiswa/{nim}/krs/{id_krs}',    [KrsController::class, 'show']);
    Route::put('/mahasiswa/{nim}/krs/{id_krs}',    [KrsController::class, 'update']);
    Route::delete('/mahasiswa/{nim}/krs/{id_krs}', [KrsController::class, 'destroy'])
        ->middleware('jwt:admin-mahasiswa,super-admin');

    // KRS DETAIL
    Route::get('/mahasiswa/{nim}/krs/{id_krs}/detail',                [KrsController::class, 'detailIndex']);
    Route::post('/mahasiswa/{nim}/krs/{id_krs}/detail',               [KrsController::class, 'detailStore']);
    Route::delete('/mahasiswa/{nim}/krs/{id_krs}/detail/{id_detail}', [KrsController::class, 'detailDestroy']);
});