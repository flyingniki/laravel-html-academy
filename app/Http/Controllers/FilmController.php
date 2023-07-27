<?php

namespace App\Http\Controllers;

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
     * @return Responsable
     */
    public function store()
    {
        return $this->success([], 201);
    }

    /**
     * Получение информации о фильме.
     *
     * @return Responsable
     */
    public function show()
    {
        return $this->success([]);
    }

    /**
     * Редактирование фильма.
     *
     * @return Responsable
     */
    public function update()
    {
        return $this->success([]);
    }

    /**
     * Получение списка похожих фильмов.
     *
     * @return \App\Http\Responses\Success
     */
    public function similar()
    {
        return $this->success([]);
    }
}
