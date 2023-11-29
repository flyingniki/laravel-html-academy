<?php


namespace App\Support\Import;

use App\Models\Comment;
use App\Models\Film;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AcademyCommentsRepository implements CommentsRepository
{

    /**
     * @inheritDoc
     */
    public function getComments(string $imdbId): ?Collection
    {
        $data = Http::get(config('services.academy.comments.url'), ['imdb' => $imdbId]);

        if ($data->clientError()) {
            return null;
        }

        return $data->collect()->map(function ($value) {
            return Comment::firstOrNew([
                'text' => $value['text'],
                'created_at' => Carbon::parse($value['date'])->toDateTimeString(),
            ]);
        });
    }

    public function getLatest(string $after): ?Collection
    {
        $data = Http::get(config('services.academy.comments.url'), ['after' => $after]);

        if ($data->clientError()) {
            return null;
        }

        $data = $data->collect();

        $films = Film::whereIn('imdb_id', $data->pluck('imdb_id'))->pluck('id', 'imdb_id')->toArray();

        return $data->filter(function ($value) use ($films) {
                return isset($films[$value['imdb_id']]);
            })
            ->map(function ($value) use ($films) {
                return Comment::firstOrNew([
                    'text' => $value['text'],
                    'created_at' => Carbon::parse($value['date'])->toDateTimeString(),
                    'film_id' => $films[$value['imdb_id']],
                ]);
            })
            ->filter(fn($value) => !$value->exists);
    }
}
