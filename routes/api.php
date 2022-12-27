<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/get-user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(
        [
            'success' => true
        ],
        200
    );
})->middleware(['auth:sanctum'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'emailVerifyNotification']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::group([
    'prefix' => 'genre'
], function () {
    Route::post('/create', [GenreController::class, 'store']);
});
