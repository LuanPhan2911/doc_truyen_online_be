<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;

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

Route::get('email/verify/{id}/{hash}', [AuthController::class, 'emailVerifyAccept'])->name('verification.verify');
Route::post('/email/verification_notification', [AuthController::class, 'emailVerifyNotification']);
Route::post('/forgot_password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset_password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::group([
    "prefix" => "users"
], function () {
    Route::get("/notifies/{user}", [UserController::class, "notifies"]);
    Route::get("/stories/{user}", [UserController::class, "stories"]);
    Route::post('/{user}', [UserController::class, "update"]);
    Route::get('/{user}', [UserController::class, "show"]);
});


Route::group([
    'prefix' => 'genre'
], function () {
    Route::post('/create', [GenreController::class, 'store']);
    Route::post('/edit/{genre}', [GenreController::class, 'update']);
    Route::delete("/delete/{genre}", [GenreController::class, 'destroy']);
    Route::get('/', [GenreController::class, 'index']);
});
Route::group([
    'prefix' => 'story'
], function () {

    Route::get('/', [StoryController::class, 'index']);
    Route::get("/show/{story:slug}", [StoryController::class, 'show']);
    Route::get("/{story:slug}/chapter", [ChapterController::class, "index"]);
    Route::get("/{story:slug}/chapter/{index}", [ChapterController::class, "show"]);
    Route::post("/{story:slug}/chapter/{index}/reaction", [ChapterController::class, "reaction"]);
});
Route::group([
    'prefix' => 'chapter'
], function () {
    Route::post('/create', [ChapterController::class, 'store']);
    Route::get('/', [ChapterController::class, 'index']);
});
Route::group([
    "prefix" => "admin/story"
], function () {
    Route::post('/create', [StoryController::class, 'store']);
    Route::get("/", [StoryController::class, "adminIndex"]);
    Route::post("/update/{id}", [StoryController::class, "update"]);
    Route::post("/{story:slug}/chapter/create", [ChapterController::class, "store"]);
    Route::post("/chapter/{chapterId}", [ChapterController::class, "update"]);
    Route::get("/{story:slug}/chapter/{index}", [ChapterController::class, "adminShow"]);
});
Route::group([
    "prefix" => "comments"
], function () {
    Route::post("/create", [CommentController::class, "store"])->middleware('auth:sanctum');
    Route::get("/", [CommentController::class, "index"]);
    Route::post("/{comment}/like", [CommentController::class, "like"])->middleware('auth:sanctum');
    Route::delete("{comment}", [CommentController::class, "destroy"]);
});
Route::group([
    "prefix" => "reports",
    // "middleware" => "auth:sanctum"
], function () {
    Route::post("/create", [ReportController::class, "store"]);
    Route::get("/", [ReportController::class, "index"]);
});
