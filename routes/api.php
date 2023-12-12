<?php

use App\Http\Controllers\Api\V1\Task\CompleteTaskController;
use App\Http\Controllers\Api\V1\Task\TaskController;
use App\Http\Controllers\Api\V1\Transaction\TransactionController;
use App\Http\Controllers\Api\V1\Transaction\TransactionType\TransactionTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Public route
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//Protected route
Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::get('/get-permissions', function () {
        return auth()->check()?auth()->user()->jsPermissions():0;
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('admin')->group(function(){
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::get('permissions/index-multiple', [PermissionController::class, 'indexMultiple']);
        Route::post('permissions/update-multiple', [PermissionController::class, 'updateMultiple']);
        Route::get('permissions/show-multiple/{module}', [PermissionController::class, 'showMultiple']);
        Route::apiResource('permissions', PermissionController::class);

    });

    Route::prefix('v1')->group(function(){
        Route::apiResource('/tasks', TaskController::class);
        Route::patch('/tasks/{task}/complete', CompleteTaskController::class);

        Route::apiResource('transactions', TransactionController::class);
        Route::prefix('transaction')->group(function(){
            Route::apiResource('/transaction-types', TransactionTypeController::class);
        });
    });

});
