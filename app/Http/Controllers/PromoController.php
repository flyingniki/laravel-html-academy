<?php

namespace App\Http\Controllers;

use App\Models\Film;
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
    public function store(Request $request, Film $film)
    {
        return $this->success([], 201);
    }

    /**
     * Получение промо-фильма.
     *
     * @return Responsable
     */
    public function show()
    {
        return $this->success([]);
    }
}
