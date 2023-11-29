<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoStoreRequest;
use App\Models\Film;
use App\Services\FilmService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Установка промо-фильма.
     *
     * @param \Illuminate\Http\Request $request
     * @param Film $film
     * @return Responsable
     */
    public function store(PromoStoreRequest $request, Film $film)
    {
        $film->update(['promo' => $request->boolean('promo')]);

        cache()->forget(Film::CACHE_PROMO_KEY);

        return $this->success(null, 201);
    }

    /**
     * Получение промо-фильма.
     *
     * @return Responsable
     */
    public function show(FilmService $service)
    {
        $promo = cache()->remember(Film::CACHE_PROMO_KEY, now()->addDay(), fn() => $service->getPromo());

        return $this->success($promo);
    }
}
