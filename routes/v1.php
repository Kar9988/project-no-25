<?php

use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\LikeController as UserLikeController;
use App\Http\Controllers\API\Admin\LikeController as AdminLikeController;
use App\Http\Controllers\API\Admin\PaymentController;
use App\Http\Controllers\API\Admin\UploadController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\VideoController;
use App\Http\Controllers\API\ContactUsController;
use App\Http\Controllers\API\EpisodeController;
use App\Http\Controllers\API\OAuthController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\RewardController;
use App\Http\Controllers\API\SocialiteController;
use App\Http\Controllers\API\UserBalanceController;
use App\Http\Controllers\API\UserRewardController;
use App\Http\Controllers\API\VideoController as UserVideoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ViewController;
use App\Http\Controllers\API\ViewController as AdminViewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::post('/login', [LoginController::class, 'store']);

Route::post('/register', [RegisterController::class, 'store']);

Route::post('/oauth/login', OAuthController::class);
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('plans', [PlanController::class,'index']);
    Route::delete('/account/delete', [UserController::class, 'delete']);
    Route::get('/auth/user', [AuthController::class, 'getAuthUser']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('likes', UserLikeController::class)->names([
        'index' => 'userLikes.index',
        'store' => 'userLikes.store',
        'show' => 'userLikes.show',
        'update' => 'userLikes.update',
        'destroy' => 'userLikes.destroy',
    ]);
    Route::get('history', [EpisodeController::class, 'showAllHistory']);
    Route::post('history', [EpisodeController::class, 'storeHistory']);
    Route::delete('history', [EpisodeController::class, 'destroyHistory']);
//    Route::get('all-history', [EpisodeController::class, 'showAllHistory']);


    Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
        Route::post('/upload/video', [UploadController::class, 'store'])->excludedMiddleware('throttle:api');
        Route::post('/balance/{id}', [UserBalanceController::class, 'store']);
        Route::apiResource('views', AdminViewController::class)->names([
            'index' => 'adminViews.index',
            'store' => 'adminViews.store',
            'show' => 'adminViews.show',
            'update' => 'adminViews.update',
            'destroy' => 'adminViews.destroy',
        ]);
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('plans', PlanController::class);
        Route::post('/video/{video}', [VideoController::class, 'update']);
        Route::apiResource('videos', VideoController::class)->except('edit', 'update');
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('likes', AdminLikeController::class)->names([
            'index' => 'adminLikes.index',
            'store' => 'adminLikes.store',
            'show' => 'adminLikes.show',
            'update' => 'adminLikes.update',
            'destroy' => 'adminLikes.destroy',
        ]);
        Route::get('categories', [CategoryController::class, 'index']);
    });
    Route::post('/contact-us', [ContactUsController::class, 'sendMail']);
    Route::get('purchase-history', [PurchaseController::class, 'index']);
    Route::post('cancel-subscription', [PurchaseController::class, 'cancelSubscription']);
    Route::post('purchase/video', [PurchaseController::class, 'store']);
    Route::post('purchase/plan', [PurchaseController::class, 'storePlan']);
    Route::post('apple/create-payment-intent', [PurchaseController::class, 'applePaymentIntent']);
    Route::get('payment-history', [PaymentController::class, 'index']);
    Route::get('payment-history/{id}', [PaymentController::class, 'getByUser']);
    Route::get('episode/source/{episodeId}', [EpisodeController::class, 'getVideoStream'])->name('episode.video');
    Route::apiResource('views', ViewController::class)->names([
        'index' => 'userViews.index',
        'store' => 'userViews.store',
        'show' => 'userViews.show',
        'update' => 'userViews.update',
        'destroy' => 'userViews.destroy',
    ]);
    Route::get('videos', [UserVideoController::class, 'index']);
    Route::get('random/videos', [UserVideoController::class, 'randomVideos']);
//    Route::get('random/episodes', [EpisodeController::class, 'randomEpisodes']);
    Route::get('video/{id}', [UserVideoController::class, 'show']);
    Route::get('discover', [UserVideoController::class, 'discover']);
    Route::get('category/{categoryId}', [UserVideoController::class, 'filter']);
    Route::get('library', [EpisodeController::class, 'library']);

    Route::group(['prefix' => 'rewards'], function () {
        Route::get('/', [RewardController::class, 'index']);
        Route::get('/user', [UserRewardController::class, 'index']);
        Route::post('/user', [UserRewardController::class, 'store']);
    });
});
