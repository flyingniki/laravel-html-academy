<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    private function registerGates()
    {
        // Удалять комментарии может только модератор или автор комментария если на него нет ответов.
        Gate::define('comment-delete', function (User $user, Comment $comment) {
            if ($user->isModerator()) {
                return true;
            }

            return $user->id === $comment->user_id && $comment->comments->isEmpty();
        });
    }
}
