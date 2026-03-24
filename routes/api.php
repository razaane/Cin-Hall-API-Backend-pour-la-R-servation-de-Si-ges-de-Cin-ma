<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;


use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;

use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SeanceController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);


    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::delete('/profile', [UserController::class, 'destroy']);

    //generate ticket 
    Route::get('/generate-ticket/{reservation_id}', [TicketController::class, 'generateTicket']); 
    //install pdf   
    Route::get('/ticket/{ticket_id}/pdf', [TicketController::class, 'generationPdf']);

    // Films
    Route::get('/films', [FilmController::class, 'index']);
    Route::get('/films/{id}', [FilmController::class, 'show']);

    // Genres
    Route::get('/genres', [GenreController::class, 'index']);
    Route::get('/genres/{id}', [GenreController::class, 'show']);

    // Seances
    Route::get('/seances/filter', [SeanceController::class, 'filter']);
    Route::get('/seances', [SeanceController::class, 'index']);
    Route::get('/seances/{id}', [SeanceController::class, 'show']);

    // Route pour créer une réservation (avec timer 15min)
    Route::post('/reservations', [ReservationController::class, 'store']);
    
    // Route pour payer avec Stripe
    Route::post('/payments/stripe', [PaymentController::class, 'payWithStripe']);

    //
    Route::get('rooms/{room}/seances', [ReservationController::class,'showSeances']);
    Route::post('reservations', [ReservationController::class,'store']);
    Route::get('reservations/{reservation}', [ReservationController::class,'show']);

    Route::apiResource('rooms', RoomController::class);



    Route::middleware('isAdmin')->prefix('admin')->group(function () {

        Route::get('/users', [UserController::class, 'index']);

        //dashbord admin
        Route::get('/dashboard', [DashboardController::class, 'index']);

        //Films
        Route::post('/films', [FilmController::class, 'store']);
        Route::put('/films/{id}', [FilmController::class, 'update']);
        Route::delete('/films/{id}', [FilmController::class, 'destroy']);

        //Genres
        Route::post('/genres', [GenreController::class, 'store']);
        Route::delete('/genres/{id}', [GenreController::class, 'destroy']);

        //Seances
        Route::post('/seances', [SeanceController::class, 'store']);
        Route::put('/seances/{id}', [SeanceController::class, 'update']);
        Route::delete('/seances/{id}', [SeanceController::class, 'destroy']);

    });


});



