<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Booking\Controllers\BookingController;

Route::prefix('bookings')
        ->controller(BookingController::class)
        ->middleware('auth:sanctum')
        ->group(function(){


            Route::get('/','index');

            Route::post('/','store');

            Route::get('/{booking}','show');


            Route::middleware(
                'role:staff,admin,super-admin'
            )
            ->group(function(){


                Route::post(
                    '/{booking}/approve',
                    'approve'
                );


                Route::post(
                    '/{booking}/reject',
                    'reject'
                );


                Route::post(
                    '/{booking}/cancel',
                    'cancel'
                );


                Route::post(
                    '/{booking}/create-trip',
                    'createTrip'
                );

                Route::post(
                    '/{booking}/restore',
                    'restore'
                );

                Route::put('/{booking}', 'update');


            });


            Route::middleware(
                'role:super-admin'
            )
            ->group(function(){

                Route::delete('/{booking}', 'destroy');

            });


});