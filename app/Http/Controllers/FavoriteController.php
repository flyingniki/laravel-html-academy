<?php

namespace App\Http\Controllers;

use App\Exceptions\RequestException;
use App\Models\Film;
use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов добавленных пользователем в избранное.
     *
     * @return Responsable
     */
    public function index()
    {
        $films = Auth::user()->films()->get(Film::LIST_FIELDS)->toArray();

        return $this->success($films);
    }

    /**
     * Добавление фильма в избранное.
     *
     * @param \Illuminate\Http\Request $request
     * @param Film $film
     * @return Responsable
     */
    public function store(Film $film)
    {
        $user = Auth::user();
        throw_if($user->hasFilm($film), new RequestException("Переданный фильм уже в избранном"));

        $user->films()->attach($film);

        return $this->success(null, 201);
    }

    /**
     * Удаление фильма из избранного.
     *
     * @param  Film $film
     * @return Responsable
     */
    public function destroy(Film $film)
    {
        $user = Auth::user();
        throw_unless($user->hasFilm($film), new RequestException("Переданный фильм не находится избранном"));

        $user->films()->detach($film);

        return $this->success(null, 201);
    }
}
