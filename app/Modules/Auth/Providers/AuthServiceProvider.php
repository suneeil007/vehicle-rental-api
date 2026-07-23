<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Auth\Repositories\Contracts\AuthRepositoryInterface;


class AuthServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );
    }


    public function boot(): void
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../Database/Migrations'
        );

    }

}
