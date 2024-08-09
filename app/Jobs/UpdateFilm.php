<?php

namespace App\Jobs;

use App\Exceptions\FilmsRepositoryException;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use App\Support\Import\FilmsRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateFilm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Film $film)
    {
    }

    public function handle(FilmsRepository $repository, FilmService $service)
    {
        // Получение информации
        $data = $repository->getFilm($this->film->imdb_id);

        if(empty($data)) {
            throw new FilmsRepositoryException('Отсутствуют данные для обновления');
        }

        $this->film = $data['film'];

        // Скачивание файлов и установка значений
        foreach ($data['links'] as $field => $link) {
            if (!empty($link)) {
                $this->film->$field = $service->saveFile($link, $field, $this->film->id);
            }
        }

        DB::beginTransaction();

        $genresIds = [];
        foreach ($data['genres'] as $genre) {
            $genresIds[] = Genre::firstOrCreate(['name' => $genre])->id;
        }

        $this->film->status = Film::STATUS_ON_MODERATION;
        $this->film->save();
        $this->film->genres()->attach($genresIds);

        DB::commit();

        GetComments::dispatch($this->film);
    }
}
