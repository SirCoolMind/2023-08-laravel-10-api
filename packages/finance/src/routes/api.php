<?php

use HafizRuslan\Finance\app\Http\Controllers\FinanceDashboardController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyAccountController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyCategoryController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/finance/v1'], function () {
    // Admin
    Route::group(['middleware' => ['auth:api']], function () {
        Route::apiResource('transaction', MoneyTransactionController::class);
        Route::apiResource('admin/money-category', MoneyCategoryController::class);
        Route::apiResource('admin/money-account', MoneyAccountController::class);
    });

    // Public
    Route::group([], base_path('packages/finance/src/routes/lookup.php'));
});

Route::group(['prefix' => 'api/finance/v2'], function () {
    // Admin
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('transaction', [FinanceDashboardController::class, 'transactionListing']);
        Route::get('dashboard/account-balance', [FinanceDashboardController::class, 'accountBalanceListing']);
    });
});
