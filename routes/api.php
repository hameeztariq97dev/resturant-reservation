<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;

Route::post('/reservations', [ReservationController::class, 'store']);
Route::delete('/reservations/{code}', [ReservationController::class, 'cancel']);
