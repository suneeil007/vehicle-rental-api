<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Modules\Vehicle\Providers\VehicleServiceProvider::class,   
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Branch\Providers\BranchServiceProvider::class,
    App\Modules\User\Providers\UserServiceProvider::class,
    App\Modules\User\Providers\RoleServiceProvider::class,
    App\Modules\Trip\Providers\TripServiceProvider::class,
    App\Modules\Booking\Providers\BookingServiceProvider::class,
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
    App\Modules\Invoice\Providers\InvoiceServiceProvider::class,
    App\Modules\Invoice\Providers\InvoiceServiceProvider::class,
    App\Modules\Dashboard\Providers\DashboardServiceProvider::class,
];

