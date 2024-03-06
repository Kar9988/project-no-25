<?php

use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\VideoController;
use App\Http\Controllers\API\EpisodeController;
use App\Http\Controllers\API\VideoController as UserVideoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ViewController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::middleware('auth:api')->group(function () {
    Route::delete('/delete', [UserController::class, 'delete']);
    Route::get('/auth/user', [AuthController::class, 'getAuthUser']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
        Route::apiResource('views', ViewController::class);
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('plans', PlanController::class);
        Route::post('/video/{video}', [VideoController::class, 'update']);
        Route::apiResource('videos', VideoController::class)->except('edit', 'update');
        Route::apiResource('categories', CategoryController::class);
        Route::get('categories', [CategoryController::class, 'index']);
    });
    Route::get('videos', [UserVideoController::class, 'index']);
});
Route::get('episode/source/{episodeId}', [EpisodeController::class, 'getVideoStream'])->name('episode.video');
