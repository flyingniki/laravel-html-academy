<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('/register', 'register')->name('auth.register');
        Route::post('/login', 'login')->name('auth.login');
        Route::post('/logout', 'logout')->middleware('auth:sanctum')->name('auth.logout');
    });

Route::controller(UserController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', 'show')->name('user.show');
        Route::patch('/user', 'update')->name('user.update');
    });

Route::get('/films/{film}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::controller(CommentController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/films/{film}/comments', 'store')->name('comments.store');
        Route::patch('/comments/{comment}',  'update')->name('comments.update');
        Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy');
    });

Route::controller(FavoriteController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/favorite',  'index')->name('favorite.index');
        Route::post('/films/{film}/favorite', 'store')->name('favorite.store');
        Route::delete('/films/{film}/favorite', 'destroy')->name('favorite.destroy');
    });

Route::controller(FilmController::class)
    ->group(function () {
        Route::get('/films', 'index')->name('films.index');
        Route::post('/films', 'store')->middleware('auth:sanctum')->name('films.store');
        Route::get('/films/{film}', 'show')->name('films.show');
        Route::patch('/films/{film}', 'update')->middleware('auth:sanctum')->name('films.update');
        Route::get('/films/{film}/similar', 'similar')->name('films.similar');
    });

Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::patch('/genres/{genre}', [GenreController::class, 'update'])->middleware(['auth:sanctum', 'role:isModerator'])->name('genres.update');

Route::get('/promo', [PromoController::class, 'show'])->name('promo.show');
Route::post('/promo/{film}', [PromoController::class, 'store'])->middleware(['auth:sanctum', 'role:isModerator'])->name('promo.store');
