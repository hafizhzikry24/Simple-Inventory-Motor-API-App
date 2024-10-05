<?php

use App\Http\Controllers\IPWhitelistController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\SparePartMotorController;
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



Route::middleware(['api', 'ip-whitelists'])->prefix('users')->group(function () {
    Route::get('', [UserController::class, 'index']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::post('', [UserController::class, 'store']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
});


Route::middleware(['api', 'ip-whitelists'])->prefix('motors')->group(function () {
    Route::get('', [MotorController::class, 'index']);
    Route::get('{id}', [MotorController::class, 'show']);
    Route::post('', [MotorController::class, 'store']);
    Route::put('{id}', [MotorController::class, 'update']);
    Route::delete('{id}', [MotorController::class, 'destroy']);
});


Route::middleware(['api', 'ip-whitelists'])->prefix('mobils')->group(function () {
    Route::get('', [MobilController::class, 'index']);
    Route::get('{id}', [MobilController::class, 'show']);
    Route::post('', [MobilController::class, 'store']);
    Route::put('{id}', [MobilController::class, 'update']);
    Route::delete('{id}', [MobilController::class, 'destroy']);
});


Route::middleware(['api', 'ip-whitelists'])->prefix('offices')->group(function () {
    Route::get('', [OfficeController::class, 'index']);
    Route::get('{id}', [OfficeController::class, 'show']);
    Route::post('', [OfficeController::class, 'store']);
    Route::put('{id}', [OfficeController::class, 'update']);
    Route::delete('{id}', [OfficeController::class, 'destroy']);
});

Route::middleware(['api'])->prefix('ip-whitelists')->group(function () {
    Route::get('', [IPWhitelistController::class, 'index']);
    Route::post('', [IPWhitelistController::class, 'store']);
    Route::get('/{id}', [IPWhitelistController::class, 'show']);
    Route::put('/{id}', [IPWhitelistController::class, 'update']);
    Route::delete('/{id}', [IPWhitelistController::class, 'destroy']);
});

Route::middleware(['api', 'ip-whitelists'])->prefix('sparepartmotors')->group(function () {
    Route::get('', [SparePartMotorController::class, 'index']);
    Route::post('', [SparePartMotorController::class, 'store']);
    Route::get('/{id}', [SparePartMotorController::class, 'show']);
    Route::put('/{id}', [SparePartMotorController::class, 'update']);
    Route::delete('/{id}', [SparePartMotorController::class, 'destroy']);
    Route::post('/import', [SparePartMotorController::class, 'import']);
    Route::post('/export', [SparePartMotorController::class, 'export']);

});

