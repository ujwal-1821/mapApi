<?php

use App\Http\Controllers\DistanceController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::post('/map/geocode', [MapController::class, 'geocode'])->name('map.geocode');
Route::post('/locations', [MapController::class, 'store'])->name('locations.store');
Route::get('/all-locations', [MapController::class, 'show'])->name('map.show');
Route::get('/map/show/{id}', [MapController::class, 'showLocation'])->name('map.showLocation');

Route::get('/distance', [DistanceController::class, 'index'])->name('distance.index');
Route::post('/distance/calc', [DistanceController::class, 'calculate'])->name('distance.calculate');



