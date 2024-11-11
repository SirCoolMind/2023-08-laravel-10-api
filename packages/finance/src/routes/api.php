<?php

use HafizRuslan\Finance\app\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/finance/v1'], function () {
    // Admin
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::apiResource('transaction', TransactionController::class);

        Route::group([], base_path('packages/kpop-collection/src/routes/lookup.php'));
    });

    // Public
});
