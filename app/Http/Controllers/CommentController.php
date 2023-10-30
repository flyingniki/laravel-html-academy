<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Models\Comment;
use App\Models\Film;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Получение списка комментариев к фильму.
     *
     * @return Responsable
     */
    public function index(Film $film)
    {
        return $this->success($film->comments()->latest()->get());
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Responsable
     */
    public function store(CommentStoreRequest $request, Film $film)
    {
        $film->comments()->create([
            'parent_id' => $request->parent_id,
            'rating' => $request->rating,
            'text' => $request->text,
            'user_id' => Auth::id(),
        ]);

        return $this->success(null, 201);
    }

    /**
     * Редактирование комментария.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return Responsable
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        $comment->update($request->validated());

        return $this->success($comment);
    }

    /**
     * Удаление комментария.
     *
     * @param  \App\Models\Comment  $comment
     * @return Responsable
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('comment-delete', $comment);

        $comment->comments()->delete();
        $comment->delete();

        return $this->success([], 201);
    }
}
