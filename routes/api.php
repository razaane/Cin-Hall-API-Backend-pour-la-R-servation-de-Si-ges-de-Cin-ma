<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;


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

});
// Routes qui nécessitent d'être connecté
Route::middleware('auth:sanctum')->group(function () {
    
    // Route pour créer une réservation (avec timer 15min)
    Route::post('/reservations', [ReservationController::class, 'store']);
    
    // Route pour payer avec Stripe
    Route::post('/payments/stripe', [PaymentController::class, 'payWithStripe']);
    
});