<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Film;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Получение списка комментариев к фильму.
     *
     * @return Responsable
     */
    public function index(Film $film)
    {
        return $this->success([]);
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Responsable
     */
    public function store(Request $request, Film $film)
    {
        return $this->success([], 201);
    }

    /**
     * Редактирование комментария.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return Responsable
     */
    public function update(Request $request, Comment $comment)
    {
        return $this->success([]);
    }

    /**
     * Удаление комментария.
     *
     * @param  \App\Models\Comment  $comment
     * @return Responsable
     */
    public function destroy(Comment $comment)
    {
        return $this->success([], 201);
    }
}
