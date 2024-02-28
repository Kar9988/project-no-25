<?php

use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserBalanceController;
use App\Http\Controllers\API\UserController;
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
Route::middleware('auth:api')->group(function () {
    Route::delete('/delete', [UserController::class, 'delete']);
    Route::get('/auth/user', [AuthController::class, 'getAuthUser']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('user/balance', UserBalanceController::class);
    Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
        Route::apiResource('users', AdminUserController::class)->only(['update', 'index', 'show', 'destroy']);
        Route::apiResource('plans', PlanController::class);
    });
});
