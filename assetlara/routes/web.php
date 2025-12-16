<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Server-rendered Blade views for traditional web interface.
| For API/React integration, see routes/api.php
|
*/

// ============================================
// GUEST ROUTES (Only for non-logged in users)
// ============================================
Route::middleware('guest')->group(function () {
    // Redirect root to login
    Route::get('/', fn() => redirect()->route('login'));

    // Login
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');

    // Registration
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->middleware('throttle:5,1');
});

// ============================================
// ADMIN ROUTES (Requires auth + admin role)
// ============================================
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Assets Resource (except show - employees can view their assigned assets)
    Route::resource('assets', AssetController::class)->except(['show']);

    // Assignment Actions
    Route::post('assets/{asset}/assign', [AssignmentController::class, 'assign'])->name('assets.assign');
    Route::post('assets/{asset}/return', [AssignmentController::class, 'return'])->name('assets.return');

    // Users Resource
    Route::resource('users', UserController::class);

    // Categories Resource
    Route::resource('categories', CategoryController::class);
});

// ============================================
// EMPLOYEE ROUTES (Requires auth + employee role)
// ============================================
Route::middleware(['auth', 'employee'])->group(function () {
    // Employee's assigned assets view
    Route::get('/my-assets', [AssetController::class, 'myAssets'])->name('my-assets');
});

// ============================================
// AUTHENTICATED ROUTES (All logged in users)
// ============================================
Route::middleware('auth')->group(function () {
    // Asset show - accessible to all authenticated users (policy controls access)
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');

    // Logout
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ROUTE STRUCTURE SUMMARY
|--------------------------------------------------------------------------
|
| GUEST:
|   GET    /                    - Redirect to login
|   GET    /login               - Show login form
|   POST   /login               - Process login
|   GET    /register            - Show registration form
|   POST   /register            - Process registration
|
| ADMIN:
|   GET    /dashboard           - Admin dashboard
|
|   GET    /assets              - List assets
|   GET    /assets/create       - Create asset form
|   POST   /assets              - Store new asset
|   GET    /assets/{id}         - Show asset
|   GET    /assets/{id}/edit    - Edit asset form
|   PUT    /assets/{id}         - Update asset
|   DELETE /assets/{id}         - Delete asset
|   POST   /assets/{id}/assign  - Assign asset
|   POST   /assets/{id}/return  - Return asset
|
|   GET    /users               - List users
|   GET    /users/create        - Create user form
|   POST   /users               - Store new user
|   GET    /users/{id}          - Show user
|   GET    /users/{id}/edit     - Edit user form
|   PUT    /users/{id}          - Update user
|   DELETE /users/{id}          - Delete user
|
|   GET    /categories          - List categories
|   GET    /categories/create   - Create category form
|   POST   /categories          - Store new category
|   GET    /categories/{id}     - Show category
|   GET    /categories/{id}/edit- Edit category form
|   PUT    /categories/{id}     - Update category
|   DELETE /categories/{id}     - Delete category
|
| EMPLOYEE ONLY:
|   GET    /my-assets           - Employee's assigned assets
|
| ALL AUTHENTICATED:
|   POST   /logout              - Logout
|   GET    /profile             - Edit profile form
|   PUT    /profile             - Update profile
|
*/
