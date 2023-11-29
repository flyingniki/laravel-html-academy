<?php

namespace Tests\Unit;

use App\Models\Film;
use App\Support\Import\AcademyFilmsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AcademyFilmsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AcademyFilmsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new AcademyFilmsRepository();
    }

    /**
     * Проверка получения информации о фильме из репозитория.
     * Ожидается получение модели Film, массива с названиями жанров и ссылками на файлы.
     */
    public function testGetFilm()
    {
        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/film-academy-1.json'))),
        ]);

        $result = $this->repository->getFilm('tt0944947');

        $this->assertInstanceOf(Film::class, $result['film']);
        $this->assertIsArray($result['genres']);
        $this->assertIsArray($result['links']);
        $this->assertFalse($result['film']->exists);
    }

    /**
     * Проверка получения пустого ответа при запросе несуществующего фильма из репозитория.
     */
    public function testGetNotFoundShows()
    {
        Http::fake([
            '*' => Http::response('{}', 404),
        ]);

        $result = $this->repository->getFilm('tt0944947');

        $this->assertNull($result);
    }
}
