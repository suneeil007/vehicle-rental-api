<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Payment\Controllers\PaymentController;

Route::prefix('payments')
    ->controller(PaymentController::class)
    ->middleware('auth:sanctum')
    ->group(function () {

        // View payments
        Route::get('/', 'index');
        Route::get('/{payment}', 'show');

        // Record payment
        Route::middleware('role:staff,admin,super-admin')
            ->group(function () {

                Route::post('/', 'store');

            });
    });