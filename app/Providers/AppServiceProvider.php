<?php

namespace App\Providers;

use App\Support\Import\FilmsRepository;
use Illuminate\Support\ServiceProvider;
use App\Support\Import\CommentsRepository;
use App\Support\Import\AcademyFilmsRepository;
use App\Support\Import\AcademyCommentsRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FilmsRepository::class, AcademyFilmsRepository::class);
        $this->app->bind(CommentsRepository::class, AcademyCommentsRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
