<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов добавленных пользователем в избранное.
     *
     * @return Responsable
     */
    public function index()
    {
        return $this->success([]);
    }

    /**
     * Добавление фильма в избранное.
     *
     * @return Responsable
     */
    public function store()
    {
        return $this->success([], 201);
    }

    /**
     * Удаление фильма из избранного.
     *
     * @return Responsable
     */
    public function destroy()
    {
        return $this->success([], 201);
    }
}
