<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Booth\AuthController as BoothAuthController;
use App\Http\Controllers\Api\Admin\RegistrantController as AdminRegistrantController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegistrantController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\Booth\ScanController;
use App\Http\Controllers\Api\Booth\RedeemController;
use Illuminate\Support\Facades\Route;

//public
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/booth/login', [BoothAuthController::class, 'login']);
Route::get('/event', [EventController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/registrants/{id}', [RegistrantController::class, 'show']);
Route::post('/register', [RegistrationController::class, 'store']);

//Booth Staff
Route::middleware('auth.booth')->group(function () {
    Route::post('/booth/scan', [ScanController::class, 'scan']);
    Route::post('/booth/redeem', [RedeemController::class, 'redeem']);
});

//admin
Route::middleware('auth.admin')->prefix('admin')->group(function () {
    Route::get('/registrants', [AdminRegistrantController::class, 'index']);
    Route::patch('/registrants/{id}/goodie', [AdminRegistrantController::class, 'updateGoodie']);
});
