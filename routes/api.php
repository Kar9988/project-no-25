<?php

use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\Admin\VideoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\RegisterController;
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
    Route::get('/auth/user', [AuthController::class, 'getAuthUser']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
        Route::apiResource('users', UserController::class)->only(['update', 'index', 'show', 'destroy']);
        Route::apiResource('plans', PlanController::class);
        Route::apiResource('videos', VideoController::class);
    });
});
