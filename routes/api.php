<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);


    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::delete('/profile', [UserController::class, 'destroy']);


    Route::middleware('isAdmin')->prefix('admin')->group(function () {

        Route::get('/users', [UserController::class, 'index']);

    });

    Route::middleware('auth:api')->group(function() {
    Route::get('rooms/{room}/seances', [ReservationController::class,'showSeances']);
    Route::post('reservations', [ReservationController::class,'store']);
    Route::get('reservations/{reservation}', [ReservationController::class,'show']);

Route::apiResource('rooms', RoomController::class);

});


});
