<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Contracts\Support\Responsable;
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
        return $this->success(Genre::all());
    }

    /**
     * Редактирование жанра.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Genre  $genre
     * @return Responsable
     */
    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return $this->success($genre->fresh());
    }
}
