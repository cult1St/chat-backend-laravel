<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

///user routes
Route::prefix('users')->middleware('auth:sanctum')->group(function(){
    //user details
    Route::controller(UserController::class)->group(function(){
        Route::get('me', 'details');
        Route::put('me', 'updateDetails');
    });

    //chat
    Route::controller(ChatController::class)->prefix('chats')->group(function(){
        Route::get('/', 'index');
    });

    Route::controller(ContactController::class)->prefix('contacts')->group(function(){
        Route::get('/', 'index');
        Route::post('create-contact', 'createContact');
        Route::patch("/update-name/{id}", "updateName");
    });

    ///searches
    Route::controller(SearchController::class)->group(function(){
        Route::get('search-by-phone-contact', 'searchByPhone');
    });
});
