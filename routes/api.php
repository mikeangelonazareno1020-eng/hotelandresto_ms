<?php

use App\Http\Controllers\AdministratorGpsController;
use App\Http\Controllers\Api\VehicleLocationController;
use App\Http\Controllers\Api\GpsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Vehicle GPS endpoints (protected for IoT devices)
Route::post('/vehicle-locations', [VehicleLocationController::class, 'store'])
    ->middleware(['device.auth','throttle:120,1']);
Route::get('/vehicle-locations/latest/{vehicle}', [VehicleLocationController::class, 'latestByVehicle']);
Route::get('/vehicle-locations/latest', [VehicleLocationController::class, 'latestAll']);
Route::get('/vehicle-locations/history/{vehicle}', [VehicleLocationController::class, 'history']);

// ESP32 POST endpoint
Route::post('/gps/update', [GpsController::class, 'update'])
    ->middleware(['device.auth','throttle:120,1']);
