<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Api\QuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('account/{user}', [AuthController::class, 'update']);
        Route::post('user/auth', [AuthController::class, 'auth']);
        Route::apiResource('questions', QuestionController::class);
        Route::resources([
            'colores' => ColorController::class,
            'user' => UserController::class,
            'role' => RoleController::class,
            'permission' => PermissionController::class,
        ], ['except' => ['create', 'edit', 'show']]);

    });
});
