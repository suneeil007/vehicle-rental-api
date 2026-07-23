<?php

namespace App\Modules\Payment\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Payment\Repositories\PaymentRepository;
use App\Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentServiceProvider extends ServiceProvider
{


    public function register(): void
    {
      $this->app->bind(
        PaymentRepositoryInterface::class,
        PaymentRepository::class

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
