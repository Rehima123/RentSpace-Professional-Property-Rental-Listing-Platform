<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — RentSpace
|--------------------------------------------------------------------------
|
| Public routes require no authentication.
| Protected routes require a valid Sanctum session.
| Role routes additionally check user role via RoleMiddleware.
|
*/

/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Public Property Routes
|--------------------------------------------------------------------------
*/
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/featured', [PropertyController::class, 'featured']);
Route::get('/properties/cities', [PropertyController::class, 'cities']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);
Route::get('/amenities', [PropertyController::class, 'amenities']);

/*
|--------------------------------------------------------------------------
| Protected Routes (all roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Owner Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:owner,admin')->prefix('owner')->name('owner.')->group(function () {
        // Property management
        Route::get('/properties', [PropertyController::class, 'ownerIndex'])->name('properties.index');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        Route::delete('/properties/{property}/images/{image}', [PropertyController::class, 'destroyImage'])->name('properties.images.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Property approval
        Route::patch('/properties/{property}/status', [PropertyController::class, 'updateStatus'])->name('properties.status');
    });
});
