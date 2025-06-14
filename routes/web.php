<?php

use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\DatabaseTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Shipment routes
Route::resource('shipments', ShipmentController::class);

// API routes
Route::get('/api/track', [ShipmentController::class, 'track'])->name('api.track');

// Sync route dengan middleware web
Route::middleware(['web'])->group(function () {
    Route::post('/api/sync-databases', [ShipmentController::class, 'syncDatabases'])->name('api.sync');
});

// Database test route
Route::get('/test-db', [DatabaseTestController::class, 'testConnections']);
