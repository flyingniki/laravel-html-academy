<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Получение профиля пользователя.
     *
     * @return Responsable
     */
    public function show()
    {
        return $this->success([]);
    }

    /**
     * Обновление профиля пользователя.
     *
     * @return Responsable
     */
    public function update()
    {
        return $this->success([]);
    }
}
