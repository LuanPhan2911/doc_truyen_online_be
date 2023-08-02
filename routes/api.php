<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
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

Route::get('/get_user', [AuthController::class, 'user'])->middleware('auth:sanctum');
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
Route::post('/email/verification_notification', [AuthController::class, 'emailVerifyNotification']);
Route::post('/forgot_password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset_password', [AuthController::class, 'resetPassword'])->name('password.reset');


Route::post('users/{user}', [UserController::class, "update"]);
Route::get('users/{user}', [UserController::class, "show"]);

Route::group([
    'prefix' => 'genre'
], function () {
    Route::post('/create', [GenreController::class, 'store']);
    Route::get('/', [GenreController::class, 'index']);
});
Route::group([
    'prefix' => 'story'
], function () {

    Route::get('/', [StoryController::class, 'index']);
    Route::get('/show/{story}', [StoryController::class, 'show']);
    Route::get("{story}/chapter", [ChapterController::class, "index"]);
});
Route::group([
    'prefix' => 'chapter'
], function () {
    Route::post('/create', [ChapterController::class, 'store']);
    Route::get('/', [ChapterController::class, 'index']);
    Route::get('/show', [ChapterController::class, 'show']);
});
Route::group([
    "prefix" => "admin/story"
], function () {
    Route::post('/create', [StoryController::class, 'store']);
    Route::get("/", [StoryController::class, "adminIndex"]);
    Route::post("/update/{id}", [StoryController::class, "update"]);
    Route::post("/chapter/create", [ChapterController::class, "store"]);
});
