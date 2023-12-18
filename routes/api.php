<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RateStoryController;
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

Route::group([
    "controller" => AuthController::class
], function () {
    Route::get('/get_user',  'user')->middleware('auth:sanctum');
    Route::post('/login',  'login');
    Route::post('/register',  'register');
    Route::get('/logout',  'logout')->middleware('auth:sanctum');
    Route::get('email/verify/{id}/{hash}',  'emailVerifyAccept')->name('verification.verify');
    Route::post('/email/verification_notification',  'emailVerifyNotification');
    Route::post('/forgot_password',  'forgotPassword')->name('password.email');
    Route::post('/reset_password',  'resetPassword')->name('password.reset');
});


Route::group([
    "prefix" => "users",
    "controller" => UserController::class
], function () {
    Route::get("/notifies/{user}",  "notifies");
    Route::get("/stories_reading",  "storiesReading");
    Route::delete('/stories_reading/{story}',  "destroyReading");
    Route::post('/{user}',  "update");
    Route::get('/{user}', "show");
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
    Route::post("/{story:slug}/rate", [RateStoryController::class, "store"]);
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
    Route::get("/{story}", [StoryController::class, 'adminShow']);
    Route::post("/{story}/chapter/create", [ChapterController::class, "store"]);
    Route::post("/chapter/{chapterId}", [ChapterController::class, "update"]);
    Route::get("/{story}/chapter/{index}", [ChapterController::class, "adminShow"]);

    Route::get("/{story}/chapter", [ChapterController::class, "index"]);
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
