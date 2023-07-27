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

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/films/{film}/comments', [CommentsController::class, 'index'])->name('comments.index');
Route::post('/films/{film}/comments', [CommentsController::class, 'store'])->name('comments.store');
Route::patch('/comments/{comment}', [CommentsController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentsController::class, 'destroy'])->name('comments.destroy');

Route::get('/favorite', [FavoriteController::class, 'index'])->name('favorite.index');
Route::post('/films/{film}/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
Route::delete('/films/{film}/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');

Route::get('/films', [FilmController::class, 'index'])->name('films.index');
Route::post('/films', [FilmController::class, 'store'])->name('films.store');
Route::get('/films/{film}', [FilmController::class, 'show'])->name('films.show');
Route::patch('/films/{film}', [FilmController::class, 'update'])->name('films.update');

Route::get('/films/{film}/similar', [FilmController::class, 'similar'])->name('films.similar');

Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::patch('/genres/{genre}', [GenreController::class, 'update'])->name('genres.update');

Route::get('/promo', [PromoController::class, 'show'])->name('promo.show');
Route::post('/promo/{film}', [PromoController::class, 'store'])->name('promo.store');

Route::get('/user', [UserController::class, 'show'])->name('user.show');
Route::patch('/user', [UserController::class, 'update'])->name('user.update');
