<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

///auth routes
Route::prefix('auth')->controller(AuthController::class)->group(function(){
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::delete('logout', 'logout');
});

//add broadcast routes here
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/broadcasting/auth', function(Request $request){
        return Broadcast::auth($request);
    });
});
