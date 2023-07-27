<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Получение списка комментариев к фильму.
     *
     * @return Responsable
     */
    public function index()
    {
        return $this->success([]);
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @return Responsable
     */
    public function store()
    {
        return $this->success([], 201);
    }

    /**
     * Редактирование комментария.
     *
     * @return Responsable
     */
    public function update()
    {
        return $this->success([]);
    }

    /**
     * Удаление комментария.
     *
     * @return Responsable
     */
    public function destroy()
    {
        return $this->success([], 201);
    }
}
