<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\FilesController;
use App\Http\Controllers\V1\UsersController;
use App\Http\Controllers\V1\BrandsController;
use App\Http\Controllers\V1\OrdersController;
use App\Http\Controllers\V1\MainPageController;
use App\Http\Controllers\V1\PaymentsController;
use App\Http\Controllers\V1\ProductsController;
use App\Http\Controllers\V1\UserAuthController;
use App\Http\Controllers\V1\AdminAuthController;
use App\Http\Controllers\V1\CategoriesController;
use App\Http\Controllers\V1\UserProfileController;
use App\Http\Controllers\V1\OrderStatusesController;

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

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::get('logout', [AdminAuthController::class, 'logout']);
        Route::post('user-listing', [UsersController::class, 'index']);
        Route::put('user-edit/{user:uuid}', [UsersController::class, 'edit']);
        Route::delete('user-delete/{user:uuid}', [UsersController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'user'], function () {
    Route::post('create', [UserAuthController::class, 'create']);
    Route::post('login', [UserAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api', 'user']], function () {
        Route::get('logout', [UserAuthController::class, 'logout']);
        Route::get('/', [UserProfileController::class, 'show']);
        Route::get('/orders', [OrdersController::class, 'index']);
        Route::delete('/', [UserProfileController::class, 'delete']);
        Route::put('/edit', [UserProfileController::class, 'update']);
    });
});

Route::group(['prefix' => 'main'], function () {
    Route::get('promotions', [MainPageController::class, 'promotions']);
    Route::get('blog', [MainPageController::class, 'posts']);
    Route::get('blog/{post:uuid}', [MainPageController::class, 'showPost']);
});

Route::group(['prefix' => 'category'], function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::get('{category:uuid}', [CategoriesController::class, 'show']);

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [CategoriesController::class, 'store']);
        Route::put('{category:uuid}', [CategoriesController::class, 'update']);
        Route::delete('{category:uuid}', [CategoriesController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'brand'], function () {
    Route::get('/', [BrandsController::class, 'index']);
    Route::get('{brand:uuid}', [BrandsController::class, 'show']);

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [BrandsController::class, 'store']);
        Route::put('{brand:uuid}', [BrandsController::class, 'update']);
        Route::delete('{brand:uuid}', [BrandsController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'product'], function () {
    Route::get('/', [ProductsController::class, 'index']);
    Route::get('{product:uuid}', [ProductsController::class, 'show']);

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [ProductsController::class, 'store']);
        Route::put('{product:uuid}', [ProductsController::class, 'update']);
        Route::delete('{product:uuid}', [ProductsController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'order-status'], function () {
    Route::get('/', [OrderStatusesController::class, 'index']);
    Route::get('{order_status:uuid}', [OrderStatusesController::class, 'show']);

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [OrderStatusesController::class, 'store']);
        Route::put('{order_status:uuid}', [OrderStatusesController::class, 'update']);
        Route::delete('{order_status:uuid}', [OrderStatusesController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'payments', 'middleware' => ['auth:api']], function () {
    Route::post('create', [PaymentsController::class, 'store'])->middleware('user');

    Route::group(['middleware' => 'admin'], function () {
        Route::get('/', [PaymentsController::class, 'index']);
        Route::get('{payment:uuid}', [PaymentsController::class, 'show']);
        Route::put('{payment:uuid}', [PaymentsController::class, 'update']);
        Route::delete('{payment:uuid}', [PaymentsController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'file'], function () {
    Route::get('{file:uuid}', [FilesController::class, 'show']);
    Route::post('/upload', [FilesController::class, 'store'])->middleware('auth:api');
});

Route::group(['prefix' => 'orders', 'middleware' => ['auth:api', 'admin']], function () {
    Route::get('/', [OrdersController::class, 'index']);
    Route::get('dashboard', [OrdersController::class, 'index']);
});

Route::group(['prefix' => 'order', 'middleware' => ['auth:api']], function () {
    Route::get('{order:uuid}', [OrdersController::class, 'show']);
    Route::post('create', [OrdersController::class, 'store'])->middleware('user');

    Route::group(['middleware' => 'admin'], function () {
        Route::put('{order:uuid}', [OrdersController::class, 'update']);
        Route::delete('{order:uuid}', [OrdersController::class, 'destroy']);
    });
});
