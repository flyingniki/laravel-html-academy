<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FilmModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка вычисления значения rating, пользовательской оценки фильма.
     * Ожидается среднее значение округленное по правилам математики.
     */
    public function testGetRating()
    {
        Film::factory()
            ->has(Comment::factory(3)->sequence(['rating' => 1], ['rating' => 2], ['rating' => 1]))
            ->create();

        $this->assertEquals(1.3, Film::first()->rating); // 4/3 = 1.3333(3)
    }

    /**
     * Проверка метода сортировки.
     * В зависимости от переданных параметров, ожидается сортировка по году выпуска или рейтингу.
     */
    public function testGetOrderedFilms()
    {
        $film1 = Film::factory()
            ->has(Comment::factory()->state(['rating' => 5]))
            ->create(['released' => 2001]);

        $film2 = Film::factory()
            ->has(Comment::factory()->state(['rating' => 1]))
            ->create(['released' => 2002]);

        $film3 = Film::factory()
            ->has(Comment::factory(2)->sequence(['rating' => 2], ['rating' => 3]))
            ->create(['released' => 2003]);

        $this->assertEquals([$film3->id, $film2->id, $film1->id], Film::ordered()->pluck('id')->toArray());
        $this->assertEquals([$film1->id, $film3->id, $film2->id], Film::ordered('rating')->pluck('id')->toArray());
        $this->assertEquals([$film1->id, $film2->id, $film3->id], Film::ordered(orderTo: 'asc')->pluck('id')->toArray());
    }

    /**
     * Проверка состояния флага "В избранном", для гостя.
     */
    public function testCheckFavoriteAttributeForGuest()
    {
        $film = Film::factory()->create();

        User::factory()->hasAttached($film)->create();

        $this->assertFalse($film->is_favorite);
    }

    /**
     * Проверка состояния флага "В избранном", для пользователя имеющего этот фильм в списке.
     */
    public function testCheckFavoriteAttributeForUser()
    {
        $film = Film::factory()->create();

        $user = User::factory()->hasAttached($film)->create();
        Sanctum::actingAs($user);

        $this->assertTrue($film->is_favorite);
    }

    /**
     * Проверка состояния флага "В избранном", для пользователя не имеющего этот фильм в списке.
     */
    public function testCheckFavoriteAttributeForUserWithoutFavorite()
    {
        $film = Film::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->assertFalse($film->is_favorite);
    }
}
