<?php

use App\Http\V1\Controllers\AdminController;
use Illuminate\Http\Request;
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

Route::group(['prefix'=>'v1'], function() {

    Route::get('/', function () {
        return ['message' => 'Welcome', 'success' => 1];
    });

    Route::group(['middleware' => ['auth:api']], function() {
        Route::get('user', function(Request $request) {
            return ['message' => 'Welcome', 'success' => 1, 'user' => $request->user()];
        });
    });

    Route::group(['prefix'=>'admin'], function() {
        Route::post('create', [AdminController::class, 'register']);
    });

});
