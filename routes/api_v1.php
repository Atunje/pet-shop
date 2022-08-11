<?php

use App\Http\Controllers\V1\AdminController;
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
    return ['message' => 'Welcome', 'success' => 1];
});

Route::group(['prefix'=>'admin'], function() {
    Route::post('create', [AdminController::class, 'register']);
    Route::post('login', [AdminController::class, 'login']);

    Route::group(['middleware' => ['auth']], function() {
        Route::get('logout', [AdminController::class, 'logout']);
    });
});


