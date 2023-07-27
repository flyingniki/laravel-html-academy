<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Регистрация пользователя.
     *
     * @return Responsable
     */
    public function register()
    {
        return $this->success([], 201);
    }

    /**
     * Авторизация и создания токена аутентификации.
     *
     * @return Responsable
     */
    public function login()
    {
        return $this->success([]);
    }

    /**
     * Удаление токена аутентификации.
     *
     * @return Responsable
     */
    public function logout()
    {
        return $this->success([], 204);
    }
}
