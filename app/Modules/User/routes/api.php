<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;


Route::middleware('auth:sanctum')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Logged-in User Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [UserController::class, 'myProfile']
    );



    /*
    |--------------------------------------------------------------------------
    | Admin User Management
    |--------------------------------------------------------------------------
    */

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



    /*
    |--------------------------------------------------------------------------
    | View Specific User Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/users/{user}/profile',
        [UserController::class, 'profile']
    );


});