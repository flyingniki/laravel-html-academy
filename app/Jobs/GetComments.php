<?php

namespace App\Jobs;

use App\Models\Film;
use App\Support\Import\CommentsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Film $film)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CommentsRepository $repository)
    {
        $this->film->comments()->saveMany($repository->getComments($this->film->imdb_id));
    }
}
