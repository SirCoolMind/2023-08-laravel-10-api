<?php

use HafizRuslan\Finance\app\Http\Controllers\MoneyCategoryController;
use HafizRuslan\Finance\app\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/finance/v1'], function () {
    // Admin
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::apiResource('transaction', TransactionController::class);
        Route::apiResource('admin/money-category', MoneyCategoryController::class);
    });

    // Public
    Route::group([], base_path('packages/finance/src/routes/lookup.php'));
});
