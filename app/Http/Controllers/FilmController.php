<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     *
     * @return Responsable
     */
    public function index()
    {
        return $this->success([]);
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Responsable
     */
    public function store(Request $request)
    {
        return $this->success([], 201);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  \App\Models\Film  $film
     * @return Responsable
     */
    public function show(Film $film)
    {
        return $this->success([]);
    }

    /**
     * Редактирование фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Film  $film
     * @return Responsable
     */
    public function update(Request $request, Film $film)
    {
        return $this->success([]);
    }

    /**
     * Получение списка похожих фильмов.
     *
     * @param Film $film
     * @return \App\Http\Responses\Success
     */
    public function similar(Film $film)
    {
        return $this->success([]);
    }
}
