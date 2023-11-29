<?php

namespace Tests\Unit;

use App\Jobs\GetComments;
use App\Jobs\UpdateFilm;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use App\Support\Import\FilmsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateFilmJobTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateFilm()
    {
        Queue::fake();

        $localFileUrl = 'http://example.localhost/storage/file.ext';
        $externalFileUrl = 'http://example.com/file.ext';

        $genres = Genre::factory(3)->create();
        $film = Film::factory()->pending()->create();
        $data = [
            'film' => $film,
            'genres' => $genres->pluck('name')->toArray(),
            'links' => [
                'poster_image' => $externalFileUrl,
                'preview_image' => $externalFileUrl,
                'background_image' => $externalFileUrl,
                'video_link' => $externalFileUrl,
                'preview_video_link' => $externalFileUrl,
            ],
        ];

        $repository = $this->mock(FilmsRepository::class, function (MockInterface $mock) use ($data) {
            $mock->shouldReceive('getFilm')->andReturn($data)->once();
        });

        $service = $this->mock(FilmService::class, function (MockInterface $mock) use ($localFileUrl) {
            $mock->shouldReceive('saveFile')->andReturn($localFileUrl)->times(5);
        });

        (new UpdateFilm($film))->handle($repository, $service);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'status' => Film::STATUS_ON_MODERATION,
            'poster_image' => $localFileUrl,
        ]);

        Queue::assertPushed(function (GetComments $job) use ($film) {
            return $job->film === $film;
        });
    }
}
