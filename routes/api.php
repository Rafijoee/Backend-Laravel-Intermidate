<?php

use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('actify/{token}', [AuthController::class, "actifyAccount"])->middleware('api')->name('actify-account');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('tes', [AuthController::class, 'tes']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('tes', [AuthController::class, 'tes']);