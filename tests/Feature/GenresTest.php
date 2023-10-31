<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GenresTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка получения списка жанров.
     * Ожидается получение всех жанров, без пагинации.
     */
    public function testGetGenresRoute()
    {
        $count = random_int(2, 10);
        Genre::factory()->count($count)->create();

        $response = $this->getJson(route('genres.index'));

        $response->assertStatus(200);
        $response->assertJsonCount($count, 'data');
        $response->assertJsonStructure(['data' => [['id', 'name']]]);
        $response->assertJsonMissing(['links' => []]);
    }

    /**
     * Проверка обновления жанра модератором.
     */
    public function testUpdateUserRoute()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $genre = Genre::factory()->create();
        $new = Genre::factory()->make();

        $response = $this->patchJson(route('genres.update', $genre), $new->toArray());

        $response->assertStatus(200);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $new->name,
        ]);
    }

    /**
     * Проверка попытки изменения жанра обычным пользователем.
     */
    public function testCheckRoleForUpdateGenreRoute()
    {
        $genre = Genre::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->patchJson(route('genres.update', $genre->id), []);

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Неавторизованное действие.']);
    }
}
