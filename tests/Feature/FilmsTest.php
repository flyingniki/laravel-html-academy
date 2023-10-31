<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FilmsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка получения списка фильмов.
     * Ожидается получение правильной структуры, и созданного к-ва.
     */
    public function testGetFilmsRoute()
    {
        $count = random_int(2, 10);
        Film::factory()->count($count)->hasAttached(Genre::factory())->create();
        $response = $this->getJson(route('films.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [], 'links' => [], 'total']);
        $response->assertJsonFragment(['total' => $count]);
    }

    /**
     * Проверка получения списка фильмов по жанру.
     * Ожидается что будут возвращены только фильмы с указанным жанром.
     * Указываем одинаковый год выпуска для исключения изменения порядка (дефолтной сортировки).
     */
    public function testGetFilmsByGenre()
    {
        $genre = Genre::factory()->create();
        $count = 2;
        $films = Film::factory($count)->hasAttached($genre)->create(['released' => 2000]);
        Film::factory(3)->create();

        $response = $this->getJson(route('films.index', ['genre' => $genre->name]));
        $result = $response->json('data');

        $response->assertStatus(200);
        $response->assertJsonFragment(['total' => $count]);
        $this->assertEquals($films->pluck('id')->toArray(), Arr::pluck($result, 'id'));
    }

    /**
     * Проверка, что по умолчанию возвращаются только готовые фильмы.
     */
    public function testGetReadyFilms()
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        Film::factory()->create(['status' => Film::STATUS_PENDING]);

        $response = $this->getJson(route('films.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $film->id]);
    }

    /**
     * Проверка, что модератор может запросить список фильмов на модерации.
     */
    public function testGetNotReadyFilmsForModerator()
    {
        Sanctum::actingAs(User::factory()->moderator()->create());

        $film = Film::factory()->create(['status' => Film::STATUS_ON_MODERATION]);
        Film::factory()->create(['status' => Film::STATUS_READY]);
        Film::factory()->create(['status' => Film::STATUS_PENDING]);

        $response = $this->getJson(route('films.index', ['status' => Film::STATUS_ON_MODERATION]));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $film->id]);
    }

    /**
     * Проверка получения списка фильмов отсортированных по рейтингу, по возрастанию.
     */
    public function testOrderedGetFilms()
    {
        $film1 = Film::factory()
            ->has(Comment::factory()->state(['rating' => 5]))
            ->create(['released' => 2001]);

        $film2 = Film::factory()
            ->has(Comment::factory()->state(['rating' => 1]))
            ->create(['released' => 2002]);

        $film3 = Film::factory()
            ->has(Comment::factory()->sequence(['rating' => 3]))
            ->create(['released' => 2003]);

        $response = $this->getJson(route('films.index', ['order_by' => 'rating', 'order_to' => 'asc']));
        $result = $response->json('data');

        $response->assertStatus(200);
        $this->assertEquals([$film2->id, $film3->id, $film1->id], Arr::pluck($result, 'id'));
    }

    public function testCreateFilmRoute()
    {
        $this->markTestSkipped('Требуется авторизация');
        $response = $this->postJson(route('films.store'));

        $response->assertStatus(201);
    }

    /**
     * Проверка получения информации о фильме.
     * Ожидается возвращение дополнительно генерируемых полей в дополнение к информации из БД.
     */
    public function testGetOneFilmRoute()
    {
        $film = Film::factory()
            ->has(Comment::factory(3)->sequence(['rating' => 1], ['rating' => 2], ['rating' => 1]))
            ->create(['released' => 2001]);

        $response = $this->getJson(route('films.show', $film->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $film->name,
            'scores_count' => 3,
            'rating' => 1.3,
        ]);
    }

    /**
     * Проверка получения информации о фильме.
     * Аутентифицированный пользователь должен видеть информацию о наличии фильма в избранном.
     */
    public function testGetOneFilmByUser()
    {
        $film = Film::factory()->create();
        $user = User::factory()->hasAttached($film)->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('films.show', $film->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $film->name,
            'is_favorite' => true,
        ]);
    }

    public function testUpdateFilmRoute()
    {
        $this->markTestSkipped('Требуется авторизация');

        $response = $this->patchJson(route('films.update', 1));

        $response->assertStatus(200);
    }

    /**
     * Проверка получения похожих фильмов.
     * На основании принадлежности к одному жанру.
     */
    public function testGetSimilarFilmsRoute()
    {
        $genre = Genre::factory()->create();
        $film = Film::factory()->hasAttached($genre)->create();
        $film2 = Film::factory()->hasAttached($genre)->create();
        $film3 = Film::factory()->hasAttached(Genre::factory())->create();

        $response = $this->getJson(route('films.similar', $film->id));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $film2->id]);
        $response->assertJsonMissing(['id' => $film3->id]);
    }

    /**
     * Заменить на обращение к несуществующему фильму
     */
    public function testWrongRoute()
    {
        $response = $this->getJson(route('films.show', 404));

        $response->assertStatus(404);
        $response->assertJsonStructure(['message', 'errors' => ['exception']]);
        $response->assertJsonFragment(['message' => 'Страница не найдена']);
    }
}
