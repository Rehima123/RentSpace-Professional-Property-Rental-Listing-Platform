<?php

use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\InquiryController;
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

    // Favorites (any authenticated user)
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/properties/{property}/favorite', [FavoriteController::class, 'toggle']);

    // Inquiries — tenant sending
    Route::middleware('role:tenant')->group(function () {
        Route::get('/tenant/inquiries', [InquiryController::class, 'tenantIndex']);
        Route::post('/properties/{property}/inquiries', [InquiryController::class, 'store']);
        Route::get('/tenant/dashboard', [DashboardController::class, 'tenant']);
    });

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

        // Inquiry management
        Route::get('/inquiries', [InquiryController::class, 'ownerIndex'])->name('inquiries.index');
        Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.status');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'owner'])->name('dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Property approval
        Route::patch('/properties/{property}/status', [PropertyController::class, 'updateStatus'])->name('properties.status');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // User management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');
    });
});
