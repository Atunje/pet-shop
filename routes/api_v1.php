<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UsersController;
use App\Http\Controllers\V1\BrandsController;
use App\Http\Controllers\V1\UserAuthController;
use App\Http\Controllers\V1\AdminAuthController;
use App\Http\Controllers\V1\CategoriesController;
use App\Http\Controllers\V1\UserProfileController;

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

Route::get('/', function () {
    return ['message' => 'Welcome', 'success' => 1];
});

Route::group(['prefix' => 'admin'], function () {
    Route::post('create', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api', 'can:admin']], function () {
        Route::get('logout', [AdminAuthController::class, 'logout']);

        Route::post('user-listing', [UsersController::class, 'index']);
        Route::put('user-edit/{user:uuid}', [UsersController::class, 'edit']);
        Route::delete('user-delete/{user:uuid}', [UsersController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'user'], function () {
    Route::post('create', [UserAuthController::class, 'create']);
    Route::post('login', [UserAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api', 'can:user']], function () {
        Route::get('logout', [UserAuthController::class, 'logout']);

        Route::get('/', [UserProfileController::class, 'show']);
        Route::delete('/', [UserProfileController::class, 'delete']);
        Route::put('/edit', [UserProfileController::class, 'update']);
    });
});

Route::group(['prefix' => 'category'], function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::get('{category:uuid}', [CategoriesController::class, 'show']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('create', [CategoriesController::class, 'store']);
        Route::put('{category:uuid}', [CategoriesController::class, 'update']);
        Route::delete('{category:uuid}', [CategoriesController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'brand'], function () {
    Route::get('/', [BrandsController::class, 'index']);
    Route::get('{brand:uuid}', [BrandsController::class, 'show']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('create', [BrandsController::class, 'store']);
        Route::put('{brand:uuid}', [BrandsController::class, 'update']);
        Route::delete('{brand:uuid}', [BrandsController::class, 'destroy']);
    });
});
