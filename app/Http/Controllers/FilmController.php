<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Models\Film;
use App\Services\FilmService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     *
     * @return Responsable
     */
    public function index(Request $request)
    {
        $films = Film::select(Film::LIST_FIELDS)
            ->when($request->has('genre'), function ($query) use ($request) {
                $query->whereRelation('genres', 'name', $request->get('genre'));
            })
            ->when($request->has('status') && $request->user()?->isModerator(),
                function ($query) use ($request) {
                    $query->whereStatus($request->get('status'));
                },
                function ($query) {
                    $query->whereStatus(Film::STATUS_READY);
                }
            )
            ->ordered($request->get('order_by'), $request->get('order_to'))
            ->paginate(8);

        return $this->paginate($films);
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Responsable
     */
    public function store(AddFilmRequest $request)
    {
        Film::create([
            'imdb_id' => $request->input('imdb'),
            'status' => Film::STATUS_PENDING,
        ]);

        return $this->success(null, 201);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  \App\Models\Film  $film
     * @return Responsable
     */
    public function show(Film $film)
    {
        return $this->success($film->append('rating')->loadCount('scores'));
    }

    /**
     * Редактирование фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Film  $film
     * @return Responsable
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        $film->update($request->validated());

        return $this->success([]);
    }

    /**
     * Получение списка похожих фильмов.
     *
     * @param Film $film
     * @return \App\Http\Responses\Success
     */
    public function similar(Film $film, FilmService $service)
    {
        return $this->success($service->getSimilarFor($film, Film::LIST_FIELDS));
    }
}
