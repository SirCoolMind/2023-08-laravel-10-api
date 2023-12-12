<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LookupController;

/*
|--------------------------------------------------------------------------
| Lookup/Retrieve Data Routes
|--------------------------------------------------------------------------
|
| Here is where you can register customized lookup route for your application.
| Using route that contains APIResources may risk of url breaching, so you put
| it inside here. Plus point, clean/short url for readibility
|
*/

Route::prefix('lookup')->group(function(){

//Unprotected route
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);

//Protected route
Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::get('transaction-type', [LookupController::class, 'transactionType']);
    Route::get('permission-based-on-module', [LookupController::class, 'permissionBasedOnModule']);





});
});
