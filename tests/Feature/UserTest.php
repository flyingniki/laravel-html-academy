<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка вызова метода обновления пользователя не аутентифицированным пользователем.
     */
    public function testUpdateUserByGuest()
    {
        $response = $this->patchJson(route('user.update'), []);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Запрос требует аутентификации.']);
    }

    /**
     * Проверка вызова метода обновления пользователя с пустыми параметрами.
     */
    public function testValidationForUpdateUserRoute()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), []);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name', 'email']]);
        $response->assertJsonFragment([
            'name' => ['Поле Имя обязательно для заполнения.'],
            'email' => ['Поле E-Mail адрес обязательно для заполнения.']
        ]);
    }

    /**
     * Проверка вызова метода обновления пользователя с уже занятым email.
     * Ожидается ошибка сообщающая о занятости переданного адреса.
     */
    public function testEmailUniqueValidationForUpdateUserRoute()
    {
        $other = User::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), ['email' => $other->email]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email' => ['Такое значение поля E-Mail адрес уже существует.']
        ]);
    }

    /**
     * Проверка вызова метода обновления пользователя без изменения email.
     * Ожидается что запрос будет выполнен успешно.
     * (текущий email адрес пользователя не учитывается при проверке существующих адресов)
     */
    public function testByPassEmailValidationForUpdateUserRoute()
    {
        $user = User::factory()->create();
        $new = User::factory()->make();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('user.update'), ['email' => $user->email, 'name' => $new->name]);

        $response->assertStatus(200);
    }

    /**
     * Проверка вызова метода обновления профиля с изменением email адреса и загрузкой аватара.
     */
    public function testUpdateUser()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $newUser = User::factory()->make();
        $file = UploadedFile::fake()->image('photo1.jpg');

        $data = [
            'email' => $newUser->email,
            'name' => $newUser->name,
            'password' => $newUser->password,
            'password_confirmation' => $newUser->password,
            'file' => $file,
        ];

        $response = $this->patchJson(route('user.update'), $data);

        $response->assertJsonFragment([
            'name' => $newUser->name,
            'email' => $newUser->email,
            'avatar' => $file->hashName(),
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => $newUser->name,
            'email' => $newUser->email,
            'avatar' => $file->hashName(),
        ]);
    }
}
