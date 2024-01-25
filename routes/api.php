<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\CarpoolingController;

Route::post('/sensor', [SensorController::class, 'store']);
Route::get('/sensor', [SensorController::class, 'index']);

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::group(["prefix" => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'profile']);
        Route::put('/', [ProfileController::class, 'update']);

        Route::group(['prefix' => 'documents', 'controller' => DocumentController::class], function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'delete');
        });

        Route::group(['prefix' => 'vehicles', 'controller' => VehicleController::class], function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'delete');
            Route::post('{id}/images', 'storeImage');
            Route::delete('{id}/images/{imageId}', 'deleteImage');
        });
    });

    Route::group(['prefix' => 'carpoolings', 'controller' => CarpoolingController::class], function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/mine', 'indexMine');

        Route::group(['prefix' => '{id}'], function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::delete('/', 'delete');

            Route::get('/passangers', 'indexPassanger');
            Route::get('/passangers/mine', 'showMinePassanger');
            Route::post('/passangers', 'storePassanger');
            Route::put('/passangers/{passangerId}/price', 'updatePricePassanger');
            Route::put('/passangers/{passangerId}/status', 'updateStatusPassanger');
        });
    });

    Route::group(['prefix' => 'trips', 'controller' => TripController::class], function () {
        Route::get('/', 'indexTrip');
        Route::post('/navigate', 'indexNavigation');
        Route::post('/start', 'start');
        Route::post('/{id}/end', 'end');
        Route::get('/{id}', 'show');
    });
});
