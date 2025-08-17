<?php

use App\Http\Controllers\Api\V1\CompleteTaskController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserSettingAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Public route
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // max 5 requests per minute per IP;
// Route::post('/login-sanctum', [AuthController::class, 'loginSanctum']);
// Route::post('/register', [AuthController::class, 'register']);

//Protected route
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('user-setting')->group(function () {
        Route::apiResource('account', UserSettingAccountController::class)->only(['update', 'index']);
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('/tasks', TaskController::class);
        Route::patch('/tasks/{task}/complete', CompleteTaskController::class);
    });
});
