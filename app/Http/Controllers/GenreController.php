<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return Responsable
     */
    public function index()
    {
        return $this->success([]);
    }

    /**
     * Редактирование жанра.
     *
     * @return Responsable
     */
    public function update()
    {
        return $this->success([]);
    }
}
