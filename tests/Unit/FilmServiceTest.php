<?php

namespace Tests\Unit;

use App\Services\FilmService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilmServiceTest extends TestCase
{
    /**
     * Проверка сохранения файла.
     */
    public function testSaveFile()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response(file_get_contents(base_path('tests/Fixtures/adam-miller-dBaz0xhCkPY-unsplash.jpg'))),
        ]);
        $url = 'http://academy.localhost/files/poster/the-grand-budapest-hotel-poster.jpg';

        $service = new FilmService();

        $path = $service->saveFile($url, 'poster', '1');

        $this->assertEquals(Storage::disk('public')->url('poster/1.jpg'), $path);
        Storage::disk('public')->assertExists('poster/1.jpg');
    }
}
