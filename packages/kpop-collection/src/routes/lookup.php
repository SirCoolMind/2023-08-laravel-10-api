<?php

use HafizRuslan\KpopCollection\app\Http\Controllers\LookupController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'lookup'], function () {
    Route::get('kpop-eras', [LookupController::class, 'kpopEras']);
    Route::get('kpop-versions', [LookupController::class, 'kpopVersions']);
});
