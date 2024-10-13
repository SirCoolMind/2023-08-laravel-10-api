<?php

use HafizRuslan\KpopCollection\app\Http\Controllers\KpopEraController;
use HafizRuslan\KpopCollection\app\Http\Controllers\KpopEraVersionController;
use HafizRuslan\KpopCollection\app\Http\Controllers\KpopItemController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/kpop/v1'], function(){

    // Admin
    Route::group(['middleware' => ['auth:sanctum']], function(){
        Route::apiResource('admin/kpop-era', KpopEraController::class);
        Route::apiResource('admin/kpop-era-version', KpopEraVersionController::class);
        Route::apiResource('admin/kpop-item', KpopItemController::class);

        Route::group([], base_path('packages/kpop-collection/src/routes/lookup.php'));
    });

    // Public

});

