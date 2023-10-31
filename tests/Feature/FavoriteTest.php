<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка получения списка избранных фильмов.
     */
    public function testGetFavoriteListRoute()
    {
        $user = User::factory()->has(Film::factory(2))->create();
        Sanctum::actingAs($user);

        Film::factory()->create();

        $response = $this->getJson(route('favorite.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    /**
     * Проверка добавления фильма в избранное.
     */
    public function testAddFavoriteForUserRoute()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $film = Film::factory()->create();

        $response = $this->postJson(route('favorite.store', $film));

        $response->assertStatus(201);
        $this->assertEquals($film->id, $user->films->first()->id);
    }

    /**
     * Проверка попытки повторного добавления фильма в избранное.
     * Ожидается ошибка и не добавления фильма повторно.
     */
    public function testAddFavoriteForUserAgain()
    {
        $film = Film::factory()->create();
        $user = User::factory()->hasAttached($film)->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('favorite.store', $film));

        $response->assertStatus(400);
        $this->assertCount(1, $user->films);
    }

    /**
     * Проверка удаления фильма из избранного.
     */
    public function testRemoveFavoriteFromUserRoute()
    {
        $film = Film::factory()->create();

        $user = User::factory()->hasAttached($film)->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('favorite.destroy', $film));

        $response->assertStatus(201);
        $this->assertEmpty($user->films);
    }
}
