<?php

use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\LikeController;
use App\Http\Controllers\API\EpisodeLikeController;
use App\Http\Controllers\API\LikeController as UserLikeController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\VideoController;
use App\Http\Controllers\API\EpisodeController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\SocialiteController;
use App\Http\Controllers\API\UserBalanceController;
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
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::middleware('auth:api')->group(function () {
    Route::delete('/account/delete', [UserController::class, 'delete']);
    Route::get('/auth/user', [AuthController::class, 'getAuthUser']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('likes', UserLikeController::class);
    Route::apiResource('episode/likes', EpisodeLikeController::class);
    Route::get('video/history', [EpisodeController::class, 'index']);
    Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
        Route::post('/balance/{id}', [UserBalanceController::class, 'store']);
        Route::apiResource('views', ViewController::class);
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('plans', PlanController::class);
        Route::post('/video/{video}', [VideoController::class, 'update']);
        Route::apiResource('videos', VideoController::class)->except('edit', 'update');
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('likes', LikeController::class);
        Route::get('categories', [CategoryController::class, 'index']);
    });
    Route::post('purchase/video', [PurchaseController::class, 'store']);
    Route::get('episode/source/{episodeId}', [EpisodeController::class, 'getVideoStream'])->name('episode.video');
    Route::get('videos', [UserVideoController::class, 'index']);
    Route::get('video/{id}', [UserVideoController::class, 'show']);
    Route::get('discover', [UserVideoController::class, 'discover']);
    Route::get('category/{categoryId}', [UserVideoController::class, 'filter']);
});
