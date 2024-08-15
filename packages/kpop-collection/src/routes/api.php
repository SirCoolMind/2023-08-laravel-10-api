<?php

use HafizRuslan\KpopCollection\app\Http\Controllers\KpopEraController;
use HafizRuslan\KpopCollection\app\Http\Controllers\KpopEraVersionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/kpop/v1'], function(){

    // Admin
    Route::apiResource('admin/kpop-era', KpopEraController::class);
    Route::apiResource('admin/kpop-era-version', KpopEraVersionController::class);

    // Public

});

