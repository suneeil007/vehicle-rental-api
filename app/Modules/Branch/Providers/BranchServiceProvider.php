<?php

namespace App\Modules\Branch\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Branch\Repositories\BranchRepository;
use App\Modules\Branch\Repositories\Contracts\BranchRepositoryInterface;


class BranchServiceProvider extends ServiceProvider
{


    public function register(): void
    {
        $this->app->bind(
            BranchRepositoryInterface::class,
            BranchRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../Database/Migrations'
        );

    }
 

}
