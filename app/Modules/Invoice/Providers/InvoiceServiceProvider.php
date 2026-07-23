<?php

namespace App\Modules\Invoice\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Invoice\Repositories\InvoiceRepository;
use App\Modules\Invoice\Repositories\Contracts\InvoiceRepositoryInterface;

class InvoiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InvoiceRepositoryInterface::class,
            InvoiceRepository::class
        );
    }

    public function boot(): void
    {
        // $this->loadRoutesFrom(
        //     app_path('Modules/Invoice/routes/api.php')
        // );

        $this->loadMigrationsFrom(
            app_path('Modules/Invoice/Database/Migrations')
        );
    }
}