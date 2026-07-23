<?php

namespace App\Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{

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
