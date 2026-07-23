<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {

    // Admin-only: full user management (list, create, view any, update any, delete)
    Route::middleware('role:admin,super-admin')
        ->prefix('users')
        ->controller(UserController::class)
        ->group(function () {

            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{user}', 'show');
            Route::put('/{user}', 'update');
            Route::patch('/{user}', 'update');
            Route::delete('/{user}', 'destroy');

        });

    // Any authenticated user can view their own profile (or a profile they're
    // authorized to see via controller-level check) - no role restriction here
    Route::get('/users/{user}/profile', [UserController::class, 'profile']);

});