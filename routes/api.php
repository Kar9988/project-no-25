<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::post('/home', [LoginController::class, 'store']);
Route::middleware('auth:api')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'getAuthUser']);
    Route::DELETE('/logout', [AuthController::class, 'logout']);
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('plans', PlanController::class);
    });
});
