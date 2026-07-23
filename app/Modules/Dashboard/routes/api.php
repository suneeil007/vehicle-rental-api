<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Dashboard\Controllers\DashboardController;

Route::middleware([
    'auth:sanctum',
    'role:staff,admin,super-admin'
])->prefix('dashboard')
  ->controller(DashboardController::class)
  ->group(function () {

      Route::get('/summary', 'summary');

      Route::get('/revenue-chart', 'revenueChart');

      Route::get('/vehicle-utilization', 'vehicleUtilization');
  });