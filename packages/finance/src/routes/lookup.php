<?php

use HafizRuslan\Finance\app\Http\Controllers\LookupController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'lookup'], function () {
    // Protected route
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('clear-categories-cache', [LookupController::class, 'clearCategoriesCache']);
        Route::get('get-accounts', [LookupController::class, 'getAccounts']);
        Route::get('get-categories', [LookupController::class, 'getCategories']);
        Route::get('get-sub-categories', [LookupController::class, 'getSubCategories']);
    });

    // Public route
    Route::get('get-categories-enum', [LookupController::class, 'getCategoriesEnum']);
    Route::get('get-sub-categories-enum', [LookupController::class, 'getSubCategoriesEnum']);
    Route::get('get-finance-type-enum', [LookupController::class, 'getFinanceTypeEnums']);
});
