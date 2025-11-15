<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

///user routes
Route::prefix('users')->middleware('auth:sanctum')->group(function(){
    //user details
    Route::controller(UserController::class)->group(function(){
        Route::get('me', 'details');
    });
});
