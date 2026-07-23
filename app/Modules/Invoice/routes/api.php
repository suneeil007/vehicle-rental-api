<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Invoice\Controllers\InvoiceController;

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('invoices')
        ->controller(InvoiceController::class)
        ->group(function () {

            // List invoices
            Route::get('/', 'index');

            // Single invoice by slug
            Route::get('/{slug}', 'show');
        });
});