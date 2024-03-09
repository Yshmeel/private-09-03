<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/api/register', [\App\Http\Controllers\UserController::class, 'register']);
Route::post('/api/login', [\App\Http\Controllers\UserController::class, 'authentication']);

// Airports & Flights routing
Route::get('/api/airport', [\App\Http\Controllers\FlightsController::class, 'airports']);
Route::get('/api/flight', [\App\Http\Controllers\FlightsController::class, 'flights']);

// Bookings routing
Route::post('/api/booking', [\App\Http\Controllers\BookingController::class, 'post']);
Route::get('/api/booking/{code}', [\App\Http\Controllers\BookingController::class, 'get']);
Route::get('/api/booking/{code}/seat', [\App\Http\Controllers\BookingController::class, 'getOccupiedSeats']);
Route::patch('/api/booking/{code}/seat', [\App\Http\Controllers\BookingController::class, 'selectSeat']);

Route::middleware('auth.api')->group(function() {
    Route::get('/user/booking', [\App\Http\Controllers\BookingController::class, 'getUserBookings']);
    Route::get('/user', [\App\Http\Controllers\UserController::class, 'current']);
});
