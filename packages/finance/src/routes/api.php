<?php

use HafizRuslan\Finance\app\Http\Controllers\FinanceDashboardController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyAccountController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyCategoryController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionController;
use HafizRuslan\Finance\app\Http\Controllers\MoneyTransferController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/finance/v1'], function () {
    // Admin
    Route::group(['middleware' => ['auth:api']], function () {
        Route::apiResource('transaction', MoneyTransactionController::class);
        Route::get('transaction-excel/template', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'downloadTemplate']);
        Route::get('transaction-excel/batches', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'listBatches']);
        Route::post('transaction-excel/upload', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'upload']);
        Route::get('transaction-excel/batch/{batch_no}', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'preview']);
        Route::put('transaction-excel/batch/{batch_no}/record/{id}', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'updateRecord']);
        Route::post('transaction-excel/batch/{batch_no}/accept', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'accept']);
        Route::post('transaction-excel/batch/{batch_no}/reject', [\HafizRuslan\Finance\app\Http\Controllers\MoneyTransactionExcelController::class, 'reject']);
        Route::apiResource('transfer', MoneyTransferController::class);
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
