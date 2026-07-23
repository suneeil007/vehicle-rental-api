<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Branch\Controllers\BranchController;

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('branches')
        ->controller(BranchController::class)
        ->group(function () {

            // Anyone logged in can view branches
            Route::get('/', 'index');
            Route::get('/{branch}', 'show');

            // Only admin & super-admin can create/update
            Route::middleware('role:admin,super-admin')->group(function () {

                Route::post('/', 'store');
                Route::put('/{branch}', 'update');
                Route::patch('/{branch}', 'update');

            });

            // Only super-admin can delete
            Route::middleware('role:super-admin')
                ->delete('/{branch}', 'destroy');

        });

});