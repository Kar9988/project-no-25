<?php


use App\Http\Controllers\Admin\GenresController;
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

Route::group(['middleware' => ['auth:api']], function () {
    Route::resource('plans/', PlanController::class)->except('create');
    Route::resource('genres/', GenresController::class)->except('create');
});
