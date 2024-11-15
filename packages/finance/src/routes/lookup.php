<?php

use HafizRuslan\Finance\app\Http\Controllers\LookupController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'lookup'], function () {

    // Protected route
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('clear-categories-cache', [LookupController::class, 'clearCategoriesCache']);
    });

    // Public route
    Route::get('get-categories-enum', [LookupController::class, 'getCategoriesEnum']);
    Route::get('get-sub-categories-enum', [LookupController::class, 'getSubCategoriesEnum']);
});
