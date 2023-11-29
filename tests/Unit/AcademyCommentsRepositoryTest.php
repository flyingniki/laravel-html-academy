<?php

namespace Tests\Unit;

use App\Models\Film;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Support\Import\AcademyCommentsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademyCommentsRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private AcademyCommentsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new AcademyCommentsRepository();
    }

    /**
     * Проверка получения комментариев для фильма.
     */
    public function testGetFilmComments()
    {
        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/comments-academy-1.json'))),
        ]);

        $result = $this->repository->getComments('tt0944947');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Comment::class, $result->first());
        $this->assertFalse($result->first()->exists);
    }

    /**
     * Проверка получения комментариев которые уже добавлены в базу.
     */
    public function testGetExistsComments()
    {
        $comment = Comment::factory()->create();
        $data = [
            [
                'text' => $comment->text,
                'date' => $comment->created_at->toJson(),
            ]
        ];

        Http::fake([
            '*' => Http::response(json_encode($data)),
        ]);

        $result = $this->repository->getComments('tt0944947');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Comment::class, $result->first());
        $this->assertTrue($result->first()->exists);
    }

    /**
     * Проверка получения новых комментариев, только для фильмов имеющихся в базе.
     */
    public function testGetLastComments()
    {
        $films = Film::factory(2)->create();
        $exists = Comment::factory()->create();

        $data = [
            [
                'text' => $this->faker->sentences(2, true),
                'date' => $this->faker->dateTimeBetween()->format(Carbon::ATOM),
                'imdb_id' => $films[0]->imdb_id,
            ],
            [
                'text' => $this->faker->sentences(2, true),
                'date' => $this->faker->dateTimeBetween()->format(Carbon::ATOM),
                'imdb_id' => $films[1]->imdb_id,
            ],
            [
                'text' => $this->faker->sentences(2, true),
                'date' => $this->faker->dateTimeBetween()->format(Carbon::ATOM),
                'imdb_id' => 'tt00' . random_int(1, 9999),
            ],
            [
                'text' => $exists->text,
                'date' => $exists->created_at,
                'imdb_id' => $exists->film->imdb_id,
            ],
        ];

        Http::fake([
            '*' => Http::response(json_encode($data)),
        ]);

        $result = $this->repository->getLatest(Carbon::yesterday()->toDateString());

        $one = $result->first();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Comment::class, $one);
        $this->assertEquals($data[0]['text'], $one->text);
        $this->assertEquals($films[0]['id'], $one->film_id);
    }
}
