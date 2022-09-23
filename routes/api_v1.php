<?php

use App\Http\Controllers\V1\AdminAuthController;
use App\Http\Controllers\V1\BrandsController;
use App\Http\Controllers\V1\CategoriesController;
use App\Http\Controllers\V1\FilesController;
use App\Http\Controllers\V1\MainPageController;
use App\Http\Controllers\V1\OrdersController;
use App\Http\Controllers\V1\OrderStatusesController;
use App\Http\Controllers\V1\PaymentsController;
use App\Http\Controllers\V1\ProductsController;
use App\Http\Controllers\V1\UserAuthController;
use App\Http\Controllers\V1\UserProfileController;
use App\Http\Controllers\V1\UsersController;
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

Route::get('/', function () {
    $response = ['message' => 'Welcome to '.config('app.name').' API v1.0.0', 'success' => 1];

    return response()->json($response, 200);
});

/* Admin Endpoints */
Route::group(['prefix' => 'admin'], function () {
    Route::post('create', [AdminAuthController::class, 'register'])->name('admin.create');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login');

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::post('user-listing', [UsersController::class, 'index'])->name('admin.user-listing');
        Route::put('user-edit/{user:uuid}', [UsersController::class, 'update'])->name('admin.user-update');
        Route::delete('user-delete/{user:uuid}', [UsersController::class, 'destroy'])->name('admin.user-delete');
    });
});

/* User Endpoints */
Route::group(['prefix' => 'user'], function () {
    Route::post('create', [UserAuthController::class, 'create'])->name('user.create');
    Route::post('login', [UserAuthController::class, 'login'])->name('user.login');

    Route::group(['middleware' => ['auth:api', 'user']], function () {
        Route::get('logout', [UserAuthController::class, 'logout'])->name('user.logout');
        Route::get('/', [UserProfileController::class, 'show'])->name('user.profile');
        Route::get('/orders', [OrdersController::class, 'index'])->name('user.orders');
        Route::delete('/', [UserProfileController::class, 'delete'])->name('user.delete');
        Route::put('/edit', [UserProfileController::class, 'update'])->name('user.update');
    });
});

/* Main Page Endpoints */
Route::group(['prefix' => 'main'], function () {
    Route::get('promotions', [MainPageController::class, 'promotions'])->name('promotions');
    Route::get('blog', [MainPageController::class, 'posts'])->name('blog');
    Route::get('blog/{post:uuid}', [MainPageController::class, 'showPost'])->name('blog.show');
});

/* Categories endpoints */
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::group(['prefix' => 'category'], function () {
    Route::get('{category:uuid}', [CategoriesController::class, 'show'])->name('category.show');

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [CategoriesController::class, 'store'])->name('category.create');
        Route::put('{category:uuid}', [CategoriesController::class, 'update'])->name('category.update');
        Route::delete('{category:uuid}', [CategoriesController::class, 'destroy'])->name('category.delete');
    });
});

/* Brands Endpoints */
Route::get('/brands', [BrandsController::class, 'index'])->name('brands');
Route::group(['prefix' => 'brand'], function () {
    Route::get('{brand:uuid}', [BrandsController::class, 'show'])->name('brand.show');

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [BrandsController::class, 'store'])->name('brand.create');
        Route::put('{brand:uuid}', [BrandsController::class, 'update'])->name('brand.update');
        Route::delete('{brand:uuid}', [BrandsController::class, 'destroy'])->name('brand.delete');
    });
});

/* Products Endpoints */
Route::get('/products', [ProductsController::class, 'index'])->name('products');
Route::group(['prefix' => 'product'], function () {
    Route::get('{product:uuid}', [ProductsController::class, 'show'])->name('product.show');

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [ProductsController::class, 'store'])->name('product.create');
        Route::put('{product:uuid}', [ProductsController::class, 'update'])->name('product.update');
        Route::delete('{product:uuid}', [ProductsController::class, 'destroy'])->name('product.delete');
    });
});

/* Order statuses Endpoints */
Route::get('/order-statuses', [OrderStatusesController::class, 'index'])->name('order-statuses');
Route::group(['prefix' => 'order-status'], function () {
    Route::get('{order_status:uuid}', [OrderStatusesController::class, 'show'])->name('order-status.show');

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('create', [OrderStatusesController::class, 'store'])->name('order-status.create');
        Route::put('{order_status:uuid}', [OrderStatusesController::class, 'update'])->name('order-status.update');
        Route::delete('{order_status:uuid}', [OrderStatusesController::class, 'destroy'])->name('order-status.delete');
    });
});

/* Payments Endpoints */
Route::get('/payments', [PaymentsController::class, 'index'])->middleware(['auth:api', 'admin'])->name('payments');
Route::group(['prefix' => 'payment', 'middleware' => ['auth:api']], function () {
    Route::post('create', [PaymentsController::class, 'store'])->middleware('user')->name('payment.create');

    Route::group(['middleware' => 'admin'], function () {
        Route::get('{payment:uuid}', [PaymentsController::class, 'show'])->name('payment.show');
        Route::put('{payment:uuid}', [PaymentsController::class, 'update'])->name('payment.update');
        Route::delete('{payment:uuid}', [PaymentsController::class, 'destroy'])->name('payment.delete');
    });
});

/* Files Endpoint */
Route::group(['prefix' => 'file'], function () {
    Route::get('{file:uuid}', [FilesController::class, 'show'])->name('file.show');
    Route::post('/upload', [FilesController::class, 'store'])->middleware('auth:api')->name('file.upload');
});

/* Orders Endpoint */
Route::group(['prefix' => 'orders', 'middleware' => ['auth:api', 'admin']], function () {
    Route::get('/', [OrdersController::class, 'index'])->name('orders');
    Route::get('dashboard', [OrdersController::class, 'index'])->name('orders.dashboard');
});

Route::group(['prefix' => 'order', 'middleware' => ['auth:api']], function () {
    Route::get('{order:uuid}', [OrdersController::class, 'show'])->name('order.show');
    Route::post('create', [OrdersController::class, 'store'])->middleware('user')->name('order.create');

    Route::group(['middleware' => 'admin'], function () {
        Route::put('{order:uuid}', [OrdersController::class, 'update'])->name('order.update');
        Route::delete('{order:uuid}', [OrdersController::class, 'destroy'])->name('order.delete');
    });
});
