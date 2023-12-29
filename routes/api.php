<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\SignController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleListController;
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
Route::post('/login', [SignController::class, 'login']);
Route::post('/user-registration', [UserController::class, 'store']);
Route::post('/vehicle-dropdown', [VehicleListController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    //Vehicle List
    Route::prefix('vehicle-list')->group(function(){
        Route::post('/', [VehicleListController::class, 'index']);
        Route::post('/store', [VehicleListController::class, 'store']);
        Route::get('/{id}', [VehicleListController::class, 'show']);
        Route::patch('/{id}', [VehicleListController::class, 'update']);
        Route::delete('/{id}', [VehicleListController::class, 'destroy']);
    });

    //Vehicle List
    Route::prefix('booking')->group(function(){
        Route::post('/', [BookingController::class, 'index']);
        Route::post('/store', [BookingController::class, 'store']);
    });

    //Logout
    Route::post('/logout', [SignController::class, 'logout']);
});
