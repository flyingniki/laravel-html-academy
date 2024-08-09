<?php


namespace App\Support\Import;

use App\Models\Film;
use Illuminate\Support\Facades\Http;

class AcademyFilmsRepository implements FilmsRepository
{
    /**
     * @inheritDoc
     */
    public function getFilm(string $imdbId)
    {
        $data = Http::get(trim(config('services.academy.films.url'), '/') . '/' . $imdbId);

        if ($data->clientError()) {
            return null;
        }

        $film = Film::firstOrNew(['imdb_id' => $imdbId]);

        $film->fill([
            'name' => $data['name'],
            'description' => $data['desc'],
            'director' => $data['director'],
            'starring' => $data['actors'],
            'run_time' => $data['run_time'],
            'released' => $data['released'],
        ]);

        $links = [
            'poster_image' => $data['poster'],
            'preview_image' => $data['icon'],
            'background_image' => $data['background'],
            'video_link' => $data['video'],
            'preview_video_link' => $data['preview'],
        ];

        return [
            'film' => $film,
            'genres' => $data['genres'],
            'links' => $links,
        ];
    }
}
