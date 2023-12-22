<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RateStoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use App\Models\Story;
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

    Route::get("/notifies",  "notifies");
    Route::post("/notifies/{story}",  "updateNotifies");
    Route::get("/stories",  "storiesReading");
    Route::get("/stories/paginate", "storiesReadingPaginate");
    Route::delete('/stories/{story}',  "destroyReading");
    Route::post('/{user}',  "update");
    Route::get('/{user}', "show");
});


Route::group([
    'prefix' => 'genres'
], function () {
    Route::post('/create', [GenreController::class, 'store']);
    Route::post('/{genre}/edit', [GenreController::class, 'update']);
    Route::delete("/{genre}/delete", [GenreController::class, 'destroy']);
    Route::get('/', [GenreController::class, 'index']);
});
Route::group([
    'prefix' => '/admin/authors'
], function () {
    Route::post('/create', [AuthorController::class, 'store']);
    Route::get('/', [AuthorController::class, 'index']);
});
Route::group([
    'prefix' => 'stories'
], function () {

    Route::get('/', [StoryController::class, 'index']);
    Route::get("/{story:slug}", [StoryController::class, 'show']);
    Route::get("/{story:slug}/chapter", [ChapterController::class, "index"]);
    Route::get("/{story:slug}/chapter/{index}", [ChapterController::class, "show"]);
    Route::post("/{story:slug}/chapter/{index}/reaction", [ChapterController::class, "reaction"]);
    Route::post("/{story:slug}/rate", [RateStoryController::class, "store"]);
});
Route::group([
    'prefix' => 'chapters'
], function () {
    Route::post('/create', [ChapterController::class, 'store']);
    Route::get('/', [ChapterController::class, 'index']);
});
Route::group([
    "prefix" => "admin/stories"
], function () {
    Route::post('/create', [StoryController::class, 'store']);
    Route::get("/", [StoryController::class, "adminIndex"]);
    Route::post("/{id}/update", [StoryController::class, "update"]);
    Route::get("/{story}/show", [StoryController::class, 'adminShow']);
    Route::post("/{story}/chapters/create", [ChapterController::class, "store"]);
    Route::post("/chapters/{chapterId}", [ChapterController::class, "update"]);
    Route::get("/{story}/chapters/{index}", [ChapterController::class, "adminShow"]);
    Route::get("/{story}/chapters", [ChapterController::class, "index"]);
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
