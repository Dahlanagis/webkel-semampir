<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RegionalStatisticController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sinilah rute API untuk portal Kelurahan Semampir didaftarkan.
| Semua rute secara otomatis diberi prefix '/api'.
|
*/

Route::prefix('v1')->group(function () {
    // Endpoint Statistik Wilayah, Demografi & Administratif
    Route::get('/statistik-wilayah', [RegionalStatisticController::class, 'index'])->name('api.v1.statistik-wilayah');
});
