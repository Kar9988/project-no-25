<?php

//use App\Http\Controllers\API\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('privacy-policy', function () {
    return view('policy');
});
Route::get('/video/{video}/episode/{episode}', function (\Illuminate\Http\Request $request) {

    if (strpos($request->header('User-Agent'), 'Android') !== false) {
        return redirect()->intended('https://play.google.com/store/games');
    } else if (strpos($request->header('User-Agent'), 'iPhone') || strpos($request->header('User-Agent'), 'iPad')) {
        return redirect()->intended('https://www.apple.com/app-store/');
    }
});
Route::get('/{any}', function () {
    return view('welcome'); // assuming you have an app.blade.php file in your resources/views directory
})->where('any', '.*');

