<?php

namespace App\Modules\Trip\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Trip\Repositories\TripRepository;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;

class TripServiceProvider extends ServiceProvider
{

   public function register(): void
    {
        $this->app->bind(
            TripRepositoryInterface::class,
            TripRepository::class
        );
    }

    public function boot(): void
    {

        // $this->loadRoutesFrom(
        //     __DIR__.'/../routes/api.php'
        // );


        $this->loadMigrationsFrom(
            __DIR__.'/../Database/Migrations'
        );

    }

}
