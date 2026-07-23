<?php

namespace App\Modules\Vehicle\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Vehicle\Repositories\Contracts\VehicleRepositoryInterface;
use App\Modules\Vehicle\Repositories\VehicleRepository;

use App\Modules\Vehicle\Repositories\Contracts\VehicleCategoryRepositoryInterface;
use App\Modules\Vehicle\Repositories\VehicleCategoryRepository;

class VehicleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Vehicle Repository
        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );

        // Vehicle Category Repository
        $this->app->bind(
            VehicleCategoryRepositoryInterface::class,
            VehicleCategoryRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../Database/Migrations'
        );
    }
}