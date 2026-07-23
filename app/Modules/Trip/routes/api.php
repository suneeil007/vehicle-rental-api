<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Trip\Controllers\TripController;

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('trips')
        ->controller(TripController::class)
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Customer Routes
            |--------------------------------------------------------------------------
            | These MUST come before /{trip}
            */

            // Logged-in user's trips
            Route::get('/me', 'myTrips');

            /*
            |--------------------------------------------------------------------------
            | Trip Listing
            |--------------------------------------------------------------------------
            */

            Route::get('/', 'index');

            /*
            |--------------------------------------------------------------------------
            | Single Trip Detail
            |--------------------------------------------------------------------------
            | Must be after custom routes.
            */
            Route::get('/{trip}', 'show');

            /*
            |--------------------------------------------------------------------------
            | Staff / Admin Operations
            |--------------------------------------------------------------------------
            */

            Route::middleware('role:staff,admin,super-admin')->group(function () {

                // Create
                Route::post('/', 'store');

                // Update
                Route::put('/{trip}', 'update');
                Route::patch('/{trip}', 'update');

                // Workflow
                Route::post('/{trip}/pickup', 'pickup');
                Route::post('/{trip}/start', 'start');
                Route::post('/{trip}/complete', 'complete');
                Route::post('/{trip}/cancel', 'cancel');

                // Invoice
                Route::post('/{trip}/generate-invoice', 'generateInvoice');
            });
        });
});