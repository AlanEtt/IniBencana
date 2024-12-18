<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DisasterLocationController;
use App\Http\Controllers\ShelterLocationController;
use App\Http\Controllers\AidTypeController;
use App\Http\Controllers\AidDistributionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::resource('disasters', DisasterLocationController::class);
Route::resource('shelters', ShelterLocationController::class);
Route::resource('aid-types', AidTypeController::class);
Route::resource('aid-distributions', AidDistributionController::class);


Route::get('/test-map', function () {
    return view('test_map');
});

