<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\expense\ExpenseController;
use App\Http\Controllers\role\RoleController;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public routes
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'store');
});

// Protected routes
Route::middleware(IsUserAuth::class)->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/profile', 'me');
        Route::put('/profile/{id}', 'update');
        Route::delete('/profile/{id}', 'destroy');
    });

    // Expense routes
    Route::prefix('expense')->controller(ExpenseController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    
    // Admin routes
    Route::middleware(CheckAdminRole::class)->group(function () {
        
        // Category routes
        Route::prefix('category')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Role routes
        Route::prefix('role')->controller(RoleController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });
});
