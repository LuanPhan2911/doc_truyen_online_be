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
    Route::get('/user',  'user')->middleware('auth:sanctum');
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
    "controller" => UserController::class,
    "middleware" => "auth:sanctum",
], function () {
    Route::post('/edit',  "update");

    Route::post("/stories/{story}/notifies",  "updateStoryNotifies");
    Route::get("/stories/reading/paginate", "getStoriesReadingPaginate");
    Route::get("/stories/marking/paginate", "getStoriesMarkingPaginate");
    Route::post("/stories/{story}/marking/create", "createStoryMarking");
    Route::delete('/stories/{story}/reading/delete',  "destroyStoryReading");
    Route::delete('/stories/{story}/marking/delete',  "destroyStoryMarking");
    Route::get('/{user}', "show");
});


Route::group([
    'prefix' => 'genres',
    'controller' => GenreController::class
], function () {
    Route::post('/create',  'store');
    Route::post('/{genre}/edit',  'update');
    Route::delete("/{genre}/delete",  'destroy');
    Route::get('/',  'index');
});


Route::group([
    'prefix' => '/admin/authors',
    'controller' => AuthorController::class
], function () {
    Route::post('/create',  'store');
    Route::get('/', 'index');
});
Route::group([
    'prefix' => '/authors',
    'controller' => AuthorController::class
], function () {
    Route::get('/{author:slug}/show',  'show');
});


Route::group([
    'prefix' => 'stories',
    'controller' => StoryController::class
], function () {

    Route::get('/', 'index');
    Route::get("/filter", "getFilterStory");
    Route::get("/{story:slug}",  'show');
});

Route::group([
    'prefix' => 'stories/{story:slug}',
    'controller' => RateStoryController::class
], function () {
    Route::post("/rate",  "store");
});


Route::group([
    'prefix' => 'stories/{story:slug}/chapter',
    'controller' => ChapterController::class,
], function () {
    Route::get("/",  "index");
    Route::get("/{index}", "show");
    Route::post("/{index}/reaction",  "reaction");
});

Route::group([
    'prefix' => 'chapters',
    'controller' => ChapterController::class
], function () {
    Route::get('/',  'index');
});

Route::group([
    "prefix" => "admin/stories",
    "controller" => StoryController::class,
    "middleware" => "auth:sanctum",
], function () {
    Route::post('/create',  'store');
    Route::get("/",  "adminIndex");
    Route::post("/{story:slug}/update",  "update");
    Route::get("/{story:slug}/show",  'adminShow');
});
Route::group([
    "prefix" => "admin/stories/{story:slug}/chapters",
    "controller" => ChapterController::class,
    "middleware" => "auth:sanctum",
], function () {
    Route::post("/create",  "store");
    Route::post("/{index}/update",  "update");
    Route::get("/{index}",  "adminShow");
    Route::get("/",  "index");
});

Route::group([
    "prefix" => "comments",
    'controller' => CommentController::class
], function () {
    Route::post("/create", "store")->middleware('auth:sanctum');
    Route::get("/",  "index");
    Route::get("/replies", "getRelies");
    Route::post("/{comment}/like",  "like")->middleware('auth:sanctum');
    Route::delete("{comment}", "destroy");
});


Route::group([
    "prefix" => "reports",
    "controller" => ReportController::class
    // "middleware" => "auth:sanctum"
], function () {
    Route::post("/create", "store");
    Route::get("/",  "index");
});
