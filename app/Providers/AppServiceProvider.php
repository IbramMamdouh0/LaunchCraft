<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound('files')) {
            $this->app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
        }

        if (! $this->app->bound('view')) {
            $this->app->register(\Illuminate\View\ViewServiceProvider::class);
        }
    }

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
