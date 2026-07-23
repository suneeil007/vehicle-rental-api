<?php

namespace App\Modules\Booking\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Booking\Repositories\BookingRepository;
use App\Modules\Booking\Repositories\Contracts\BookingRepositoryInterface;

class BookingServiceProvider extends ServiceProvider
{

   public function register(): void
   {
       $this->app->bind(
        BookingRepositoryInterface::class,
        BookingRepository::class
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
