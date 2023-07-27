<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Установка промо-фильма.
     *
     * @return Responsable
     */
    public function store()
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
