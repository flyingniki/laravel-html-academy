<?php

namespace Tests\Unit;

use App\Jobs\GetComments;
use App\Jobs\UpdateFilm;
use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use App\Support\Import\CommentsRepository;
use App\Support\Import\FilmsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class GetCommentsJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка добавления комментариев
     */
    public function testAddCommentsToFilm()
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        $comments = Comment::factory(5)->external()->make();

        $repository = $this->mock(CommentsRepository::class, function (MockInterface $mock) use ($comments) {
            $mock->shouldReceive('getComments')->andReturn($comments)->once();
        });

        (new GetComments($film))->handle($repository);

        $this->assertDatabaseCount(Comment::class, 5);
        $this->assertDatabaseHas('comments', [
            'text' => $comments->first()->text,
            'film_id' => $film->id,
            'user_id' => null,
        ]);

    }

    /**
     * Проверка добавления комментариев.
     * При наличии добавляемых комментариев в БД.
     * Кроме того проверяется что комментарии добавленные пользователями не теряются.
     */
    public function testAddCommentsToExistsFilm()
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        $comments = Comment::factory(2)->external()->make();
        $existsExternal = Comment::factory(2)->for($film)->create(['user_id' => null]);
        $existsLocal = Comment::factory()->for($film)->create();

        $comments = $comments->concat($existsExternal);

        $repository = $this->mock(CommentsRepository::class, function (MockInterface $mock) use ($comments) {
            $mock->shouldReceive('getComments')->andReturn($comments)->once();
        });

        (new GetComments($film))->handle($repository);

        $this->assertDatabaseCount(Comment::class, 5);
        $this->assertDatabaseHas('comments', [
            'text' => $existsExternal->first()->text,
            'film_id' => $film->id,
            'user_id' => null,
        ]);
        $this->assertDatabaseHas('comments', [
            'text' => $existsLocal->text,
            'film_id' => $film->id,
            'user_id' => $existsLocal->user_id,
        ]);
    }
}
