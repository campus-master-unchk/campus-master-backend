<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/update-my-profile', [AuthController::class, 'updateMyProfile']);
    Route::put('update-my-password',[PasswordResetController::class, 'changePassword']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::post('/create-user', [AuthController::class, 'createUser']);
    });

    Route::middleware('teacher')->prefix('teacher')->group(function () {
        //....
    });
});
    

