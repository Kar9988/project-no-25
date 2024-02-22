<?php

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
Route::get('/getAuth', function(){
    return response()->json(['auth' => auth()->user(),'status' => 400]);
})->middleware('auth:api');
Route::apiResource('plans', PlanController::class);
