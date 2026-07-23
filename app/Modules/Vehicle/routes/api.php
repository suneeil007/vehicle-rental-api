<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Vehicle\Controllers\VehicleController;
use App\Modules\Vehicle\Controllers\VehicleCategoryController;
use App\Modules\Vehicle\Controllers\VehicleImageController;

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('vehicles')
        ->controller(VehicleController::class)
        ->group(function () {

            // Anyone logged in can browse/view vehicles
            Route::get('/', 'index');
            Route::get('/{vehicle}', 'show');

            // Only admin/super-admin/staff can create/edit/delete vehicles
            Route::middleware('role:staff,admin,super-admin')->group(function () {
                Route::post('/', 'store');
                Route::put('/{vehicle}', 'update');
                Route::patch('/{vehicle}', 'update');
            });

            // Deleting a vehicle is admin-only (not staff)
            Route::middleware('role:admin,super-admin')
                ->delete('/{vehicle}', 'destroy');

        });


    Route::prefix('vehicle-categories')
        ->controller(VehicleCategoryController::class)
        ->group(function () {

            // Anyone logged in can view categories
            Route::get('/', 'index');
            Route::get('/{vehicleCategory}', 'show');

            // Only admin/super-admin manage categories
            Route::middleware('role:admin,super-admin')->group(function () {
                Route::post('/', 'store');
                Route::put('/{vehicleCategory}', 'update');
                Route::patch('/{vehicleCategory}', 'update');
                Route::delete('/{vehicleCategory}', 'destroy');
            });

        });


    Route::prefix('vehicle-images')
        ->controller(VehicleImageController::class)
        ->group(function () {

            // Anyone logged in can view images
            Route::get('/', 'index');
            Route::get('/{vehicleImage}', 'show');

            // Only admin/super-admin/staff manage images
            Route::middleware('role:staff,admin,super-admin')->group(function () {
                Route::post('/', 'store');
                Route::put('/{vehicleImage}', 'update');
                Route::patch('/{vehicleImage}', 'update');
                Route::delete('/{vehicleImage}', 'destroy');
            });

        });

});