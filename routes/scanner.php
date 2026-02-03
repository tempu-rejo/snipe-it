<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Scanner Public API Routes
|--------------------------------------------------------------------------
|
| Public API routes untuk Scanner App yang tidak memerlukan auth
|
*/

/**
 * Scanner App Public API Routes (No Authentication Required)
 */
Route::group(['prefix' => 'v1/scanner', 'middleware' => ['throttle:60,1']], function () {
    // Public routes (no auth required)
    Route::post('login', [Api\ScannerApiController::class, 'login'])->name('api.scanner.login');
    Route::get('departments', [Api\ScannerApiController::class, 'getDepartments'])->name('api.scanner.departments');
});

/**
 * Scanner App Protected API Routes (Authentication Required)
 */
Route::group(['prefix' => 'v1/scanner', 'middleware' => ['auth:api', 'throttle:120,1']], function () {
    Route::post('scan', [Api\ScannerApiController::class, 'scanAsset'])->name('api.scanner.scan');
    Route::get('profile', [Api\ScannerApiController::class, 'getProfile'])->name('api.scanner.profile');
});
