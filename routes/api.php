<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;




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



    Route::middleware('isAdmin')->prefix('admin')->group(function () {

        Route::get('/users', [UserController::class, 'index']);

        //dashbord admin
         Route::get('/admin/dashboard', [DashboardController::class, 'index']);


    });

});