<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API routes for React frontend integration.
| All routes return JSON responses using Laravel Resources.
|
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ============================================
// PROTECTED ROUTES (Sanctum Authentication)
// ============================================
Route::middleware('auth:sanctum')->group(function () {

    // --------------------------------------------
    // AUTH ROUTES
    // --------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    // --------------------------------------------
    // PROFILE ROUTES (All authenticated users)
    // --------------------------------------------
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

    // --------------------------------------------
    // EMPLOYEE ROUTES (Employee only)
    // --------------------------------------------
    Route::middleware('employee')->group(function () {
        Route::get('/my-assets', [AssetController::class, 'myAssets']);
    });

    // --------------------------------------------
    // ADMIN ROUTES
    // --------------------------------------------
    Route::middleware('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Assets Resource Routes
        Route::apiResource('assets', AssetController::class)->names([
            'index' => 'api.assets.index',
            'store' => 'api.assets.store',
            'show' => 'api.assets.show',
            'update' => 'api.assets.update',
            'destroy' => 'api.assets.destroy',
        ]);
        Route::get('/assets-available', [AssetController::class, 'available']);

        // Users Resource Routes
        Route::apiResource('users', UserController::class)->names([
            'index' => 'api.users.index',
            'store' => 'api.users.store',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'destroy' => 'api.users.destroy',
        ]);
        Route::get('/employees', [UserController::class, 'employees']);

        // Categories Resource Routes
        Route::apiResource('categories', CategoryController::class)->names([
            'index' => 'api.categories.index',
            'store' => 'api.categories.store',
            'show' => 'api.categories.show',
            'update' => 'api.categories.update',
            'destroy' => 'api.categories.destroy',
        ]);

        // Assignments
        Route::get('/assignments', [AssignmentController::class, 'index']);
        Route::get('/assignments/recent', [AssignmentController::class, 'recent']);
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show']);
        Route::post('/assets/{asset}/assign', [AssignmentController::class, 'assign']);
        Route::post('/assets/{asset}/return', [AssignmentController::class, 'return']);
    });
});

/*
|--------------------------------------------------------------------------
| API ENDPOINTS SUMMARY
|--------------------------------------------------------------------------
|
| PUBLIC:
|   POST   /api/auth/register          - Register new user
|   POST   /api/auth/login             - Login and get token
|
| AUTHENTICATED (Any user):
|   POST   /api/auth/logout            - Logout (revoke token)
|   POST   /api/auth/refresh           - Refresh token
|   GET    /api/auth/user              - Get current user
|   GET    /api/profile                - Get profile
|   PUT    /api/profile                - Update profile
|   PUT    /api/profile/password       - Update password
|   GET    /api/my-assets              - Get user's assigned assets
|
| ADMIN ONLY:
|   GET    /api/dashboard              - Dashboard stats
|
|   GET    /api/assets                 - List all assets
|   POST   /api/assets                 - Create asset
|   GET    /api/assets/{id}            - Show asset
|   PUT    /api/assets/{id}            - Update asset
|   DELETE /api/assets/{id}            - Delete asset
|   GET    /api/assets-available       - List available assets
|
|   GET    /api/users                  - List all users
|   POST   /api/users                  - Create user
|   GET    /api/users/{id}             - Show user
|   PUT    /api/users/{id}             - Update user
|   DELETE /api/users/{id}             - Delete user
|   GET    /api/employees              - List active employees
|
|   GET    /api/categories             - List all categories
|   POST   /api/categories             - Create category
|   GET    /api/categories/{id}        - Show category
|   PUT    /api/categories/{id}        - Update category
|   DELETE /api/categories/{id}        - Delete category
|
|   GET    /api/assignments            - List all assignments
|   GET    /api/assignments/recent     - Recent assignments
|   GET    /api/assignments/{id}       - Show assignment
|   POST   /api/assets/{id}/assign     - Assign asset to user
|   POST   /api/assets/{id}/return     - Return asset
|
*/
