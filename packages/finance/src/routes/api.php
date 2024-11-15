<?php

use HafizRuslan\Finance\app\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/finance/v1'], function () {
    // Admin
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::apiResource('transaction', TransactionController::class);
    });

    // Public
    Route::group([], base_path('packages/finance/src/routes/lookup.php'));
});
