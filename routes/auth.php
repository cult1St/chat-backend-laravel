<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

///auth routes
Route::prefix('auth')->controller(AuthController::class)->group(function(){
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::delete('logout', 'logout');
});