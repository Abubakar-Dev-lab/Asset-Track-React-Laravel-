# AssetLara - Complete Laravel Asset Tracking System Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Laravel Features Used](#laravel-features-used)
4. [Database Design](#database-design)
5. [Application Flow](#application-flow)
6. [File Structure Deep Dive](#file-structure-deep-dive)
7. [Authentication & Authorization](#authentication--authorization)
8. [Core Features](#core-features)
9. [Real-Time Broadcasting](#real-time-broadcasting)
10. [API Documentation (React Integration)](#api-documentation-react-integration)
11. [Testing Strategy](#testing-strategy)
12. [Setup & Installation](#setup--installation)
13. [Development Workflow](#development-workflow)
14. [Code Examples & Patterns](#code-examples--patterns)

---

## Project Overview

**AssetLara** is a full-stack Laravel 12 application designed to help organizations track their IT assets (laptops, monitors, keyboards, etc.). It implements role-based access control with two user roles: **Admin** and **Employee**.

### Key Capabilities:
- **Asset Management**: Complete CRUD operations for assets with categories
- **Assignment System**: Check-out/check-in assets to employees
- **User Management**: Admin can create and manage users
- **Real-Time Updates**: WebSocket-based notifications for asset assignments
- **Audit Trail**: Complete history of all asset assignments
- **Role-Based Access**: Different permissions for admins and employees

### Tech Stack:
- Laravel 12 Framework
- MySQL Database
- Tailwind CSS for styling
- Laravel Reverb for WebSockets
- Laravel Sanctum for API authentication
- Pest PHP for testing
- Vite for asset bundling

---

## System Architecture

### MVC Pattern Implementation

```
┌─────────────┐      ┌──────────────┐      ┌─────────────┐
│   Browser   │ ────▶│  Controller  │ ────▶│    Model    │
│  (View)     │      │  (Business   │      │   (Data)    │
│             │◀──── │   Logic)     │◀──── │             │
└─────────────┘      └──────────────┘      └─────────────┘
```

### Request Lifecycle for Asset Assignment:

```
1. User clicks "Check Out" button
   ↓
2. POST /assets/{asset}/assign (Route)
   ↓
3. Middleware (auth, admin)
   ↓
4. AssignmentController@assign
   ↓
5. AssignAssetRequest validation
   ↓
6. AssignmentService::assignAsset()
   ↓
7. Database Transaction
   - Create Assignment record
   - Update Asset status
   ↓
8. Dispatch AssetAssigned Event
   ↓
9. Broadcast via Laravel Reverb
   ↓
10. Admin dashboard receives real-time update
   ↓
11. Redirect with success message
```

---

## Laravel Features Used

### 1. **Eloquent ORM & Relationships**

The application uses Laravel's Eloquent ORM for database interactions.

**Relationship Types Used:**
- `hasMany`: User → Assignments, Asset → Assignments, Category → Assets
- `belongsTo`: Asset → Category, Assignment → User, Assignment → Asset

**Example:**
```php
// User Model
public function assignments() {
    return $this->hasMany(Assignment::class);
}

// Assignment Model
public function user() {
    return $this->belongsTo(User::class);
}
```

### 2. **Form Request Validation**

Custom Form Request classes handle validation logic separately from controllers.

**Files:**
- `StoreAssetRequest.php`
- `UpdateAssetRequest.php`
- `LoginRequest.php`
- `RegisterRequest.php`
- `AssignAssetRequest.php`

**Benefits:**
- Keeps controllers thin
- Reusable validation rules
- Authorization logic in one place

**Example:**
```php
class StoreAssetRequest extends FormRequest
{
    public function authorize() {
        return auth()->user()->role === 'admin';
    }

    public function rules() {
        return [
            'serial_number' => 'required|unique:assets',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
```

### 3. **Service Layer Pattern**

Business logic is extracted into service classes for reusability and testability.

**Services:**
- `AuthService`: Handles authentication logic
- `AssignmentService`: Manages asset check-out/check-in

**Example:**
```php
class AssignmentService
{
    public function assignAsset(Asset $asset, int $userId): Assignment
    {
        return DB::transaction(function () use ($asset, $userId) {
            $assignment = Assignment::create([...]);
            $asset->update(['status' => 'assigned']);
            AssetAssigned::dispatch($assignment);
            return $assignment;
        });
    }
}
```

### 4. **Policy-Based Authorization**

Laravel Policies define authorization logic for resources.

**AssetPolicy.php:**
```php
public function before(User $user, string $ability): ?bool
{
    // Admin bypasses all checks
    if ($user->role === 'admin') {
        return true;
    }
    return null;
}

public function view(User $user, Asset $asset): Response
{
    // Employee can only view assets assigned to them
    $isCurrentHolder = $asset->assignments()
        ->where('user_id', $user->id)
        ->whereNull('returned_at')
        ->exists();

    return $isCurrentHolder
        ? Response::allow()
        : Response::deny();
}
```

### 5. **Route Model Binding**

Laravel automatically injects model instances based on route parameters.

**Example:**
```php
// Route
Route::get('/assets/{asset}', [AssetController::class, 'show']);

// Controller
public function show(Asset $asset) {
    // $asset is automatically loaded from database
    return view('assets.show', compact('asset'));
}
```

### 6. **Database Transactions**

Critical operations use transactions to ensure data consistency.

**Example:**
```php
DB::transaction(function () use ($asset, $userId) {
    $assignment = Assignment::create([...]);
    $asset->update(['status' => 'assigned']);
    // If any query fails, everything rolls back
});
```

### 7. **Soft Deletes**

Models use soft deletes to preserve data for audit purposes.

**Models with Soft Deletes:**
- User
- Asset

**Example:**
```php
class Asset extends Model
{
    use SoftDeletes;
}

// Soft delete
$asset->delete();

// Query without soft-deleted records (default)
Asset::all();

// Include soft-deleted records
Asset::withTrashed()->get();

// Only soft-deleted records
Asset::onlyTrashed()->get();
```

### 8. **Event Broadcasting**

Real-time notifications using Laravel Events and WebSockets.

**Events:**
- `AssetAssigned`: Fired when an asset is checked out
- `AssetReturned`: Fired when an asset is checked in

**Example:**
```php
class AssetAssigned implements ShouldBroadcast
{
    public function broadcastOn(): Channel {
        return new Channel('dashboard');
    }

    public function broadcastWith(): array {
        return [
            'asset_name' => $this->assignment->asset->name,
            'user_name' => $this->assignment->user->name,
            'time' => now()->toDateTimeString(),
        ];
    }
}
```

### 9. **Middleware**

Custom middleware enforces role-based access.

**EnsureUserIsAdmin:**
```php
public function handle(Request $request, Closure $next)
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }
    return $next($request);
}
```

### 10. **Resource Controllers**

Using `Route::resource()` for RESTful routing.

**Example:**
```php
Route::resource('assets', AssetController::class);

// Generates routes:
// GET    /assets              → index()
// GET    /assets/create       → create()
// POST   /assets              → store()
// GET    /assets/{asset}      → show()
// GET    /assets/{asset}/edit → edit()
// PUT    /assets/{asset}      → update()
// DELETE /assets/{asset}      → destroy()
```

### 11. **Blade Templates & Layouts**

Master layout with reusable components.

**Layout Structure:**
```
layouts/
  └── app.blade.php (master layout)

Feature Views:
  ├── assets/
  ├── users/
  ├── categories/
  ├── auth/
  └── profile/
```

### 12. **Laravel Sanctum (API Authentication)**

API routes protected with Sanctum middleware.

**Example:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

### 13. **Query Scopes**

Local query scopes for reusable query logic.

**Example:**
```php
// Asset Model
public function scopeAvailable($query) {
    return $query->where('status', 'available');
}

// Usage
$availableAssets = Asset::available()->get();
```

### 14. **Eager Loading**

Prevent N+1 query problems with eager loading.

**Example:**
```php
// Bad (N+1 problem)
$assets = Asset::all();
foreach ($assets as $asset) {
    echo $asset->category->name; // Separate query for each asset
}

// Good (Eager loading)
$assets = Asset::with('category')->get();
foreach ($assets as $asset) {
    echo $asset->category->name; // No additional queries
}
```

---

## Database Design

### Entity Relationship Diagram

```
┌─────────────┐          ┌──────────────┐          ┌────────────┐
│    User     │          │  Assignment  │          │   Asset    │
├─────────────┤          ├──────────────┤          ├────────────┤
│ id (PK)     │◀────────▶│ user_id (FK) │          │ id (PK)    │
│ name        │          │ asset_id(FK) │◀────────▶│ name       │
│ email       │          │ assigned_by  │          │ serial_no  │
│ password    │          │ assigned_at  │          │ status     │
│ role        │          │ returned_at  │          │category_id │
│ is_active   │          │ notes        │          │ image_path │
│ timestamps  │          └──────────────┘          │ timestamps │
│ soft_deletes│                                     │soft_deletes│
└─────────────┘                                     └────────────┘
                                                          │
                                                          │
                                                    ┌─────▼──────┐
                                                    │  Category  │
                                                    ├────────────┤
                                                    │ id (PK)    │
                                                    │ name       │
                                                    │ slug       │
                                                    │ timestamps │
                                                    └────────────┘
```

### Table Schemas

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    is_active BOOLEAN DEFAULT true,
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### Categories Table
```sql
CREATE TABLE categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Assets Table
```sql
CREATE TABLE assets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    category_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    serial_number VARCHAR(255) UNIQUE NOT NULL,
    status ENUM('available', 'assigned', 'broken', 'maintenance') DEFAULT 'available',
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);
```

#### Assignments Table
```sql
CREATE TABLE assignments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    asset_id BIGINT NOT NULL,
    assigned_by BIGINT NOT NULL,
    assigned_at TIMESTAMP NOT NULL,
    returned_at TIMESTAMP NULL,
    notes TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);
```

### Database Relationships Explained

1. **User ↔ Assignment** (One-to-Many)
   - A user can have many assignments
   - Each assignment belongs to one user

2. **Asset ↔ Assignment** (One-to-Many)
   - An asset can have many assignments over time
   - Each assignment is for one asset

3. **Category ↔ Asset** (One-to-Many)
   - A category can have many assets
   - Each asset belongs to one category

4. **Assignment → User (assigned_by)** (Many-to-One)
   - Each assignment is created by one admin
   - One admin can create many assignments

---

## Application Flow

### User Registration & Login Flow

```
┌──────────────────┐
│ Visit /register  │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────┐
│ RegisterRequest validates│
│ - Name required          │
│ - Email unique           │
│ - Password min 8 chars   │
└────────┬─────────────────┘
         │
         ▼
┌─────────────────────────┐
│ AuthController creates   │
│ user with default role   │
│ 'employee'               │
└────────┬────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Auto-login & redirect to │
│ /my-assets               │
└──────────────────────────┘
```

### Asset Assignment Flow

```
┌──────────────────────┐
│ Admin views asset    │
│ /assets/{id}         │
└────────┬─────────────┘
         │
         ▼
┌───────────────────────────┐
│ Asset status: available?  │
└────────┬──────────────────┘
         │ YES
         ▼
┌──────────────────────────────┐
│ Admin selects employee from  │
│ dropdown & clicks "Check Out"│
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ POST /assets/{asset}/assign  │
│ with user_id                 │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ AssignAssetRequest validates │
│ - user_id exists             │
│ - user is active             │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ AssignmentService checks     │
│ asset status === 'available' │
└────────┬─────────────────────┘
         │ VALID
         ▼
┌──────────────────────────────┐
│ DB Transaction begins        │
│ 1. Create Assignment record  │
│ 2. Update Asset → 'assigned' │
│ 3. Dispatch AssetAssigned    │
│ COMMIT                       │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ AssetAssigned event          │
│ broadcasts to 'dashboard'    │
│ channel via WebSocket        │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ All online admins receive    │
│ real-time notification       │
└──────────────────────────────┘
```

### Asset Return Flow

```
┌──────────────────────┐
│ Admin clicks         │
│ "Check In" button    │
└────────┬─────────────┘
         │
         ▼
┌─────────────────────────────┐
│ POST /assets/{asset}/return │
└────────┬────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ AssignmentService finds      │
│ active assignment (where     │
│ returned_at IS NULL)         │
└────────┬─────────────────────┘
         │ FOUND
         ▼
┌──────────────────────────────┐
│ DB Transaction begins        │
│ 1. Update Assignment         │
│    returned_at = now()       │
│ 2. Update Asset → 'available'│
│ 3. Dispatch AssetReturned    │
│ COMMIT                       │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ AssetReturned event          │
│ broadcasts to dashboard      │
└──────────────────────────────┘
```

### Employee Asset View Flow

```
┌──────────────────────┐
│ Employee logs in     │
│ Redirects to         │
│ /my-assets           │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────────────┐
│ AssetController@myAssets     │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ Query assignments WHERE:     │
│ - user_id = auth()->id()     │
│ - returned_at IS NULL        │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ Pluck asset_ids from results │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ Load Assets WHERE            │
│ id IN (asset_ids)            │
│ with category relationship   │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ Return view with             │
│ currently held assets        │
└──────────────────────────────┘
```

---

## File Structure Deep Dive

### Project Directory Structure

```
assetlara/
├── app/
│   ├── Events/                    # Event classes
│   │   ├── AssetAssigned.php      # Fired when asset checked out
│   │   └── AssetReturned.php      # Fired when asset checked in
│   ├── Http/
│   │   ├── Controllers/           # Request handlers
│   │   │   ├── AssetController.php
│   │   │   ├── AssignmentController.php
│   │   │   ├── AuthController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ProfileController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/            # Request filters
│   │   │   └── EnsureUserIsAdmin.php
│   │   └── Requests/              # Form validation
│   │       ├── AssignAssetRequest.php
│   │       ├── LoginRequest.php
│   │       ├── RegisterRequest.php
│   │       ├── StoreAssetRequest.php
│   │       └── UpdateAssetRequest.php
│   ├── Models/                    # Eloquent models
│   │   ├── Asset.php
│   │   ├── Assignment.php
│   │   ├── Category.php
│   │   └── User.php
│   ├── Policies/                  # Authorization logic
│   │   └── AssetPolicy.php
│   ├── Providers/                 # Service providers
│   │   ├── AppServiceProvider.php
│   │   └── AuthServiceProvider.php
│   └── Services/                  # Business logic
│       ├── AssignmentService.php
│       └── AuthService.php
├── bootstrap/                     # App initialization
├── config/                        # Configuration files
│   ├── auth.php
│   ├── broadcasting.php
│   ├── database.php
│   ├── reverb.php
│   └── sanctum.php
├── database/
│   ├── factories/                 # Model factories for testing
│   │   ├── AssetFactory.php
│   │   ├── AssignmentFactory.php
│   │   ├── CategoryFactory.php
│   │   └── UserFactory.php
│   └── migrations/                # Database schema
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 2025_12_11_063037_create_categories_table.php
│       ├── 2025_12_11_063043_create_assets_table.php
│       └── 2025_12_11_063051_create_assignments_table.php
├── public/                        # Web root
│   ├── index.php                  # Entry point
│   └── storage/                   # Public storage link
│       └── assets/                # Asset images
├── resources/
│   ├── css/
│   │   └── app.css                # Tailwind CSS
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js           # Axios config
│   │   └── echo.js                # Laravel Echo config
│   └── views/                     # Blade templates
│       ├── assets/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── my-assets.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── categories/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── layouts/
│       │   └── app.blade.php       # Master layout
│       ├── profile/
│       │   └── edit.blade.php
│       ├── users/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       └── dashboard.blade.php
├── routes/
│   ├── api.php                     # API routes
│   ├── channels.php                # Broadcast channels
│   ├── console.php                 # Artisan commands
│   └── web.php                     # Web routes
├── storage/                        # File storage
│   ├── app/
│   │   └── public/
│   │       └── assets/             # Uploaded images
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/                    # Feature tests
│   │   ├── AssetAssignmentTest.php
│   │   ├── AssetManagementTest.php
│   │   ├── AuthenticationTest.php
│   │   ├── AuthorizationTest.php
│   │   ├── CategoryManagementTest.php
│   │   ├── DashboardTest.php
│   │   ├── EmployeeAssetsTest.php
│   │   ├── ExampleTest.php
│   │   ├── ProfileManagementTest.php
│   │   └── UserManagementTest.php
│   ├── Unit/                       # Unit tests
│   ├── Pest.php                    # Pest configuration
│   └── TestCase.php                # Base test case
├── vendor/                         # Composer dependencies
├── .env                            # Environment variables
├── composer.json                   # PHP dependencies
├── package.json                    # Node dependencies
├── phpunit.xml                     # PHPUnit config
└── vite.config.js                  # Vite config
```

### Key File Explanations

#### Controllers

**AssetController.php** - Handles all asset CRUD operations
- `index()` - List all assets (admin only)
- `create()` - Show asset creation form
- `store()` - Save new asset with image upload
- `show()` - Display single asset with assignment history
- `edit()` - Show asset edit form
- `update()` - Update asset details & image
- `destroy()` - Soft delete asset (only if not assigned)
- `myAssets()` - Employee view of their assigned assets

**AssignmentController.php** - Handles check-out/check-in
- `assign()` - Check out asset to employee
- `return()` - Check in asset (mark as available)

**AuthController.php** - Handles authentication
- `create()` - Show login form
- `store()` - Process login (role-based redirect)
- `createRegister()` - Show registration form
- `storeRegister()` - Process registration
- `destroy()` - Logout

#### Services

**AssignmentService.php** - Business logic for assignments
- Validates business rules (asset must be available)
- Uses database transactions
- Dispatches events

**AuthService.php** - Authentication logic
- Validates credentials
- Checks user is_active status
- Regenerates session (prevents session fixation)

#### Models

**Asset.php**
- Relationships: `category()`, `assignments()`
- Scopes: `available()`
- Soft Deletes enabled

**Assignment.php**
- No timestamps (uses manual assigned_at/returned_at)
- Relationships: `user()`, `asset()`, `admin()`
- Casts: assigned_at, returned_at as datetime

**Category.php**
- Auto-generates slug from name on create/update
- Relationship: `assets()`

**User.php**
- Uses: `HasApiTokens`, `Notifiable`, `SoftDeletes`
- Hidden: password, remember_token
- Casts: is_active (boolean), password (hashed)
- Relationship: `assignments()`

---

## Authentication & Authorization

### Authentication System

**Login Process:**
1. User submits email/password via `LoginRequest`
2. `AuthService->authenticate()` validates credentials
3. Checks `is_active` flag (prevents disabled users from logging in)
4. Session regenerated (security: prevents session fixation)
5. Role-based redirect:
   - Admin → `/dashboard`
   - Employee → `/my-assets`

**Registration Process:**
1. User submits form via `RegisterRequest`
2. Validates: unique email, password min 8 chars, password confirmation
3. Creates user with default role `'employee'`
4. Auto-login after registration
5. Redirect to `/my-assets`

**Logout Process:**
1. `AuthService->logout()` called
2. User logged out via `Auth::logout()`
3. Session invalidated
4. CSRF token regenerated
5. Redirect to login

### Authorization Layers

**Layer 1: Route Middleware**

```php
// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('assets', AssetController::class);
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
});

// All authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
    Route::get('/profile', [ProfileController::class, 'edit']);
});
```

**Layer 2: Policy Authorization**

```php
// AssetController uses authorizeResource
public function __construct() {
    $this->authorizeResource(Asset::class, 'asset');
}

// Automatically checks:
// - viewAny → before index()
// - view → before show()
// - create → before create()
// - update → before update()
// - delete → before destroy()
```

**Layer 3: Manual Authorization Checks**

```php
// In controllers
$this->authorize('update', $asset);

// In Blade views
@can('update', $asset)
    <a href="{{ route('assets.edit', $asset) }}">Edit</a>
@endcan
```

### Role-Based Permissions Matrix

| Action                      | Admin | Employee |
|-----------------------------|-------|----------|
| View all assets index       | ✅    | ❌       |
| View assigned asset details | ✅    | ✅       |
| View unassigned assets      | ✅    | ❌       |
| Create assets               | ✅    | ❌       |
| Update assets               | ✅    | ❌       |
| Delete assets               | ✅    | ❌       |
| Assign assets               | ✅    | ❌       |
| Return assets               | ✅    | ❌       |
| View all users              | ✅    | ❌       |
| Create users                | ✅    | ❌       |
| Update users                | ✅    | ❌       |
| Delete users                | ✅    | ❌       |
| View categories             | ✅    | ❌       |
| Manage categories           | ✅    | ❌       |
| View my assets              | ✅    | ✅       |
| Edit own profile            | ✅    | ✅       |
| Access dashboard            | ✅    | ❌       |

---

## Core Features

### 1. Asset Management

**Creating an Asset:**
```php
// Form validation (StoreAssetRequest)
$validated = $request->validated();

// Image upload handling
if ($request->hasFile('image')) {
    $validated['image_path'] = $request->file('image')
        ->store('assets', 'public');
}

// Create asset
Asset::create($validated);
```

**Updating an Asset:**
```php
// Handle image replacement
if ($request->hasFile('image')) {
    // Delete old image
    if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
        Storage::disk('public')->delete($asset->image_path);
    }
    // Store new image
    $validated['image_path'] = $request->file('image')
        ->store('assets', 'public');
}

$asset->update($validated);
```

**Soft Deleting an Asset:**
```php
// Check if asset is currently assigned
if ($asset->status === 'assigned') {
    return redirect()->back()
        ->with('error', 'Cannot delete an assigned asset');
}

$asset->delete(); // Soft delete
```

**Asset Status Flow:**
```
┌──────────────┐     Check Out     ┌──────────────┐
│  Available   │ ─────────────────▶│   Assigned   │
└──────────────┘                    └──────────────┘
       ▲                                    │
       │                                    │
       └────────────────────────────────────┘
                  Check In
```

### 2. Assignment System

**Check-Out Process (Detailed):**

```php
public function assignAsset(Asset $asset, int $userId): Assignment
{
    // 1. Business rule validation
    if ($asset->status !== 'available') {
        throw new Exception("Asset is currently '{$asset->status}'");
    }

    // 2. Database transaction (ensures data consistency)
    return DB::transaction(function () use ($asset, $userId) {
        // 2a. Create audit log
        $assignment = Assignment::create([
            'asset_id' => $asset->id,
            'user_id' => $userId,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        // 2b. Update asset status
        $asset->update(['status' => 'assigned']);

        // 2c. Dispatch event
        AssetAssigned::dispatch($assignment);

        return $assignment;
    });
}
```

**Why Use Transactions?**
- If any step fails, everything rolls back
- Prevents inconsistent state (e.g., assignment created but asset status not updated)
- Atomic operation (all or nothing)

**Check-In Process:**

```php
public function returnAsset(Asset $asset): Assignment
{
    // Find active assignment
    $activeAssignment = $asset->assignments()
        ->whereNull('returned_at')
        ->first();

    if (!$activeAssignment) {
        throw new Exception("Asset is not currently checked out");
    }

    return DB::transaction(function () use ($asset, $activeAssignment) {
        // Mark as returned
        $activeAssignment->update(['returned_at' => now()]);

        // Update asset status
        $asset->update(['status' => 'available']);

        // Dispatch event
        AssetReturned::dispatch($activeAssignment);

        return $activeAssignment;
    });
}
```

### 3. User Management

**Creating Users (Admin):**

Admins can create users and assign roles:

```php
public function store(RegisterRequest $request)
{
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'employee', // Admin can set role
        'is_active' => true,
    ]);
}
```

**Updating Users:**

Admins can update name, email, role, and is_active status:

```php
$request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users,email,' . $user->id],
    'role' => ['required', 'in:admin,employee'],
    'is_active' => ['required', 'boolean'],
]);

$user->update($request->only(['name', 'email', 'role', 'is_active']));
```

**Deleting Users:**

Protection rules:
1. Cannot delete yourself
2. Cannot delete user with active assignments

```php
// Check 1: Prevent self-deletion
if ($user->id === auth()->id()) {
    return redirect()->back()
        ->with('error', 'You cannot delete your own account');
}

// Check 2: Check for active assignments
$activeAssignments = $user->assignments()
    ->whereNull('returned_at')
    ->count();

if ($activeAssignments > 0) {
    return redirect()->back()
        ->with('error', "Cannot delete user with active assignments");
}

$user->delete(); // Soft delete
```

### 4. Category Management

**Auto-Generating Slugs:**

Categories automatically generate URL-friendly slugs:

```php
// Category Model
protected static function boot()
{
    parent::boot();

    static::creating(function ($category) {
        if (empty($category->slug)) {
            $category->slug = Str::slug($category->name);
        }
    });

    static::updating(function ($category) {
        if ($category->isDirty('name')) {
            $category->slug = Str::slug($category->name);
        }
    });
}
```

**Example:**
- Name: "Mobile Devices" → Slug: "mobile-devices"
- Name: "IT Equipment" → Slug: "it-equipment"

**Deletion Protection:**

Cannot delete category if it has assets:

```php
if ($category->assets()->count() > 0) {
    return redirect()->back()
        ->with('error', 'Cannot delete category with assets');
}

$category->delete();
```

---

## Real-Time Broadcasting

### WebSocket Architecture

```
┌──────────────┐        ┌──────────────┐        ┌──────────────┐
│   Laravel    │◀──────▶│   Reverb     │◀──────▶│   Browser    │
│ Application  │        │  WebSocket   │        │ (Laravel     │
│              │        │   Server     │        │  Echo)       │
└──────────────┘        └──────────────┘        └──────────────┘
```

### Broadcasting Setup

**1. Event Class (AssetAssigned.php):**

```php
class AssetAssigned implements ShouldBroadcast
{
    public $assignment;

    public function __construct(Assignment $assignment)
    {
        // Eager load relationships for broadcast
        $this->assignment = $assignment->load(['user', 'asset']);
    }

    // Define broadcast channel
    public function broadcastOn(): Channel
    {
        return new Channel('dashboard');
    }

    // Define broadcast data
    public function broadcastWith(): array
    {
        return [
            'asset_id' => $this->assignment->asset->id,
            'asset_name' => $this->assignment->asset->name,
            'user_name' => $this->assignment->user->name,
            'status' => 'assigned',
            'time' => now()->toDateTimeString(),
        ];
    }
}
```

**2. Channel Authorization (routes/channels.php):**

```php
// Admin-only dashboard channel
Broadcast::channel('dashboard', function ($user) {
    return $user->role === 'admin';
});
```

**3. Frontend Listener (resources/views/layouts/app.blade.php):**

```javascript
// Only setup for admins
@if(auth()->user()->role === 'admin')
<script>
    // Initialize Laravel Echo
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });

    // Listen for AssetAssigned events
    window.Echo.channel('dashboard')
        .listen('AssetAssigned', (e) => {
            alert(`Asset "${e.asset_name}" assigned to ${e.user_name}`);
        })
        .listen('AssetReturned', (e) => {
            alert(`Asset "${e.asset_name}" returned by ${e.user_name}`);
        });
</script>
@endif
```

### Broadcasting Workflow

```
1. Admin assigns asset to employee
   ↓
2. AssignmentService->assignAsset() executes
   ↓
3. AssetAssigned::dispatch($assignment) called
   ↓
4. Laravel queues broadcast job
   ↓
5. Job sends event to Reverb WebSocket server
   ↓
6. Reverb broadcasts to all subscribed clients on 'dashboard' channel
   ↓
7. All online admins receive real-time notification
   ↓
8. JavaScript alert displays notification
```

### Real-Time Broadcasting Troubleshooting Guide

During development, several issues were encountered and fixed to get real-time broadcasting working properly. This section documents the problems and their solutions.

#### Issue 1: Tailwind CSS CDN Warning & WebSocket Wrong Port

**Symptoms:**
```
cdn.tailwindcss.com should not be used in production
WebSocket connection to 'ws://localhost/app/?protocol=7...' failed
```

**Root Cause:**
- Layout was using CDN scripts instead of Vite-built assets
- WebSocket was trying to connect to port 80 instead of 8080

**Solution:**

1. **Updated `resources/views/layouts/app.blade.php`:**
   - Replaced CDN Tailwind script with Vite directive
   - Removed CDN Laravel Echo and Pusher scripts

```html
<!-- Before (WRONG) -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<!-- After (CORRECT) -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

2. **Fixed `.env` VITE variables:**
   - Vite doesn't resolve variable references at build time

```env
# Before (WRONG - Vite can't resolve these)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# After (CORRECT - Use actual values)
VITE_REVERB_APP_KEY=bmtpzntzxsw8adayxhfl
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

3. **Rebuilt assets:**
```bash
npm run build
```

#### Issue 2: Events Not Being Received by Frontend

**Symptoms:**
- Echo connected successfully (visible in console)
- Events dispatched on backend (verified via tinker)
- Frontend never received events

**Root Cause:**
Laravel Echo `.listen()` method needs a dot prefix (`.`) for custom event names when using `broadcastAs()`, or expects the full namespaced class name by default.

**Solution:**

1. **Added `broadcastAs()` method to Event classes:**

**File: `app/Events/AssetAssigned.php`:**
```php
class AssetAssigned implements ShouldBroadcast
{
    // ... existing code ...

    // Added this method to define a custom event name
    public function broadcastAs(): string
    {
        return 'AssetAssigned';
    }
}
```

**File: `app/Events/AssetReturned.php`:**
```php
class AssetReturned implements ShouldBroadcast
{
    // ... existing code ...

    public function broadcastAs(): string
    {
        return 'AssetReturned';
    }
}
```

2. **Updated frontend listeners with dot prefix:**

**File: `resources/views/dashboard.blade.php`:**
```javascript
// Before (WRONG - without dot prefix)
window.Echo.channel('dashboard')
    .listen('AssetAssigned', (e) => { ... })
    .listen('AssetReturned', (e) => { ... });

// After (CORRECT - with dot prefix for custom event names)
window.Echo.channel('dashboard')
    .listen('.AssetAssigned', (e) => { ... })
    .listen('.AssetReturned', (e) => { ... });
```

**Why the dot prefix?**
- Without dot: Echo expects the full class name (e.g., `App\\Events\\AssetAssigned`)
- With dot: Echo treats it as a literal event name (matches `broadcastAs()` return value)

3. **Restarted queue worker to pick up changes:**
```bash
php artisan queue:restart
# Then restart the worker
php artisan queue:work
```

#### Issue 3: Dashboard Not Updating in Real-Time

**Symptoms:**
- Alerts showed correctly
- Dashboard stats and recent assignments didn't update without page reload

**Root Cause:**
The dashboard view was server-rendered and had no JavaScript to update the DOM when events were received.

**Solution:**

**File: `resources/views/dashboard.blade.php`:**

1. Added IDs to stat elements:
```html
<dd id="available-count" class="text-2xl font-bold">{{ $availableAssets }}</dd>
<dd id="assigned-count" class="text-2xl font-bold">{{ $assignedAssets }}</dd>
```

2. Added JavaScript to handle real-time updates:
```javascript
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.channel('dashboard')
                .listen('.AssetAssigned', (e) => {
                    updateCounts('assigned');
                    addNewAssignment(e, 'assigned');
                })
                .listen('.AssetReturned', (e) => {
                    updateCounts('returned');
                });
        }
    });

    function updateCounts(action) {
        const availableEl = document.getElementById('available-count');
        const assignedEl = document.getElementById('assigned-count');

        if (action === 'assigned') {
            availableEl.textContent = parseInt(availableEl.textContent) - 1;
            assignedEl.textContent = parseInt(assignedEl.textContent) + 1;
        } else if (action === 'returned') {
            availableEl.textContent = parseInt(availableEl.textContent) + 1;
            assignedEl.textContent = parseInt(assignedEl.textContent) - 1;
        }
    }

    function addNewAssignment(data, status) {
        // Dynamically adds new assignment to the recent assignments list
        // with visual highlight that fades after 3 seconds
    }
</script>
@endpush
```

3. Added `@stack('scripts')` to layout:
```html
<!-- In layouts/app.blade.php, before </body> -->
@stack('scripts')
```

#### Summary of Files Modified for Real-Time Fix

| File | Changes Made |
|------|--------------|
| `resources/views/layouts/app.blade.php` | Replaced CDN with Vite, added `@stack('scripts')` |
| `.env` | Fixed VITE_REVERB_* variables with actual values |
| `app/Events/AssetAssigned.php` | Added `broadcastAs()` method |
| `app/Events/AssetReturned.php` | Added `broadcastAs()` method |
| `resources/views/dashboard.blade.php` | Added real-time update JavaScript |

#### Verification Checklist

After making these changes, verify real-time is working:

1. **Services running:**
   ```bash
   php artisan serve          # Laravel server
   php artisan queue:work     # Queue worker
   php artisan reverb:start   # WebSocket server
   ```

2. **Browser console should show:**
   ```
   Echo connected, listening on dashboard channel...
   ```

3. **On asset assignment, should see:**
   ```
   Dashboard: Asset assigned {id: 1, asset_name: '...', user_name: '...', ...}
   ```

4. **Dashboard stats should update automatically without page reload**

---

## API Documentation (React Integration)

The application provides a complete RESTful API for frontend integration (React, Vue, etc.) using Laravel Sanctum for authentication and API Resources for JSON transformation.

### API Architecture

```
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│  React Frontend │ ───────▶│   Laravel API   │ ───────▶│    Database     │
│  (SPA Client)   │◀─────── │  (Sanctum Auth) │◀─────── │    (MySQL)      │
└─────────────────┘  JSON   └─────────────────┘         └─────────────────┘
        │
        │ Bearer Token
        ▼
┌─────────────────┐
│  Local Storage  │
│  (JWT Token)    │
└─────────────────┘
```

### File Structure for API

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/                          # API Controllers (JSON responses)
│   │       ├── AuthController.php        # Login, Register, Logout
│   │       ├── AssetController.php       # Asset CRUD
│   │       ├── UserController.php        # User CRUD
│   │       ├── CategoryController.php    # Category CRUD
│   │       ├── AssignmentController.php  # Assignment operations
│   │       ├── DashboardController.php   # Dashboard statistics
│   │       └── ProfileController.php     # Profile management
│   └── Resources/                        # JSON Transformers
│       ├── UserResource.php
│       ├── AssetResource.php
│       ├── CategoryResource.php
│       ├── AssignmentResource.php
│       └── DashboardResource.php
routes/
├── api.php                               # API routes (RESTful)
└── web.php                               # Web routes (Blade views)
```

### Authentication Flow

#### 1. Register New User

```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
    "message": "Registration successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "employee",
        "is_active": true,
        "created_at": "2025-01-15T10:30:00.000000Z"
    },
    "token": "1|abc123xyz...",
    "token_type": "Bearer"
}
```

#### 2. Login

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "admin"
    },
    "token": "2|xyz789abc...",
    "token_type": "Bearer"
}
```

#### 3. Using the Token

All protected endpoints require the Bearer token in the Authorization header:

```http
GET /api/assets
Authorization: Bearer 2|xyz789abc...
```

#### 4. Logout

```http
POST /api/auth/logout
Authorization: Bearer 2|xyz789abc...
```

### API Endpoints Reference

#### Public Endpoints (No Authentication)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login and get token |

#### Authenticated Endpoints (Any User)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/logout` | Logout (revoke token) |
| POST | `/api/auth/refresh` | Refresh token |
| GET | `/api/auth/user` | Get current user info |
| GET | `/api/profile` | Get profile details |
| PUT | `/api/profile` | Update profile |
| PUT | `/api/profile/password` | Change password |
| GET | `/api/my-assets` | Get user's assigned assets |

#### Admin-Only Endpoints

**Dashboard:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard` | Get dashboard statistics |

**Assets (CRUD):**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/assets` | List all assets (paginated) |
| POST | `/api/assets` | Create new asset |
| GET | `/api/assets/{id}` | Get asset details |
| PUT | `/api/assets/{id}` | Update asset |
| DELETE | `/api/assets/{id}` | Delete asset (soft) |
| GET | `/api/assets-available` | List available assets |

**Users (CRUD):**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List all users (paginated) |
| POST | `/api/users` | Create new user |
| GET | `/api/users/{id}` | Get user details |
| PUT | `/api/users/{id}` | Update user |
| DELETE | `/api/users/{id}` | Delete user (soft) |
| GET | `/api/employees` | List active employees |

**Categories (CRUD):**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all categories |
| POST | `/api/categories` | Create new category |
| GET | `/api/categories/{id}` | Get category details |
| PUT | `/api/categories/{id}` | Update category |
| DELETE | `/api/categories/{id}` | Delete category |

**Assignments:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/assignments` | List all assignments |
| GET | `/api/assignments/recent` | Get recent assignments |
| GET | `/api/assignments/{id}` | Get assignment details |
| POST | `/api/assets/{id}/assign` | Assign asset to user |
| POST | `/api/assets/{id}/return` | Return asset |

### Query Parameters

Most list endpoints support filtering, sorting, and pagination:

```http
GET /api/assets?status=available&category_id=1&search=laptop&sort_by=name&sort_order=asc&per_page=20&page=1
```

| Parameter | Description | Example |
|-----------|-------------|---------|
| `status` | Filter by status | `available`, `assigned`, `broken` |
| `category_id` | Filter by category | `1`, `2`, `3` |
| `search` | Search by name/serial | `laptop`, `SN123` |
| `sort_by` | Sort field | `name`, `created_at`, `status` |
| `sort_order` | Sort direction | `asc`, `desc` |
| `per_page` | Items per page | `10`, `15`, `50` |
| `page` | Page number | `1`, `2`, `3` |

### API Resources (JSON Transformers)

API Resources transform Eloquent models into consistent JSON structures.

**AssetResource.php:**
```php
class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'image_url' => $this->image_path
                ? asset('storage/' . $this->image_path)
                : null,
            'created_at' => $this->created_at->toISOString(),
            // Conditional relationships
            'category' => new CategoryResource($this->whenLoaded('category')),
            'assignments' => AssignmentResource::collection(
                $this->whenLoaded('assignments')
            ),
        ];
    }
}
```

### React Integration Example

#### 1. API Service (apiService.js)

```javascript
const API_URL = 'http://localhost:8000/api';

// Get token from storage
const getToken = () => localStorage.getItem('token');

// API request helper
const apiRequest = async (endpoint, options = {}) => {
    const token = getToken();

    const config = {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(token && { 'Authorization': `Bearer ${token}` }),
            ...options.headers,
        },
    };

    const response = await fetch(`${API_URL}${endpoint}`, config);

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'API request failed');
    }

    return response.json();
};

// Auth API
export const authAPI = {
    login: (credentials) => apiRequest('/auth/login', {
        method: 'POST',
        body: JSON.stringify(credentials),
    }),

    register: (userData) => apiRequest('/auth/register', {
        method: 'POST',
        body: JSON.stringify(userData),
    }),

    logout: () => apiRequest('/auth/logout', { method: 'POST' }),

    getUser: () => apiRequest('/auth/user'),
};

// Assets API
export const assetsAPI = {
    getAll: (params = '') => apiRequest(`/assets${params}`),
    getOne: (id) => apiRequest(`/assets/${id}`),
    create: (data) => apiRequest('/assets', {
        method: 'POST',
        body: JSON.stringify(data),
    }),
    update: (id, data) => apiRequest(`/assets/${id}`, {
        method: 'PUT',
        body: JSON.stringify(data),
    }),
    delete: (id) => apiRequest(`/assets/${id}`, { method: 'DELETE' }),
    assign: (id, userId) => apiRequest(`/assets/${id}/assign`, {
        method: 'POST',
        body: JSON.stringify({ user_id: userId }),
    }),
    return: (id) => apiRequest(`/assets/${id}/return`, { method: 'POST' }),
};

// Dashboard API
export const dashboardAPI = {
    getStats: () => apiRequest('/dashboard'),
};
```

#### 2. Login Component (Login.jsx)

```jsx
import { useState } from 'react';
import { authAPI } from './apiService';

function Login({ onLoginSuccess }) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await authAPI.login({ email, password });

            // Store token
            localStorage.setItem('token', response.token);
            localStorage.setItem('user', JSON.stringify(response.user));

            onLoginSuccess(response.user);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            {error && <div className="error">{error}</div>}

            <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Email"
                required
            />

            <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Password"
                required
            />

            <button type="submit" disabled={loading}>
                {loading ? 'Logging in...' : 'Login'}
            </button>
        </form>
    );
}

export default Login;
```

#### 3. Assets List Component (AssetsList.jsx)

```jsx
import { useState, useEffect } from 'react';
import { assetsAPI } from './apiService';

function AssetsList() {
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(true);
    const [pagination, setPagination] = useState({});

    useEffect(() => {
        fetchAssets();
    }, []);

    const fetchAssets = async (page = 1) => {
        setLoading(true);
        try {
            const response = await assetsAPI.getAll(`?page=${page}`);
            setAssets(response.data);
            setPagination(response.meta);
        } catch (err) {
            console.error('Failed to fetch assets:', err);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div>Loading...</div>;

    return (
        <div>
            <h1>Assets</h1>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    {assets.map(asset => (
                        <tr key={asset.id}>
                            <td>{asset.name}</td>
                            <td>{asset.serial_number}</td>
                            <td>{asset.status}</td>
                            <td>{asset.category?.name}</td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {/* Pagination */}
            <div>
                {pagination.current_page > 1 && (
                    <button onClick={() => fetchAssets(pagination.current_page - 1)}>
                        Previous
                    </button>
                )}
                <span>Page {pagination.current_page} of {pagination.last_page}</span>
                {pagination.current_page < pagination.last_page && (
                    <button onClick={() => fetchAssets(pagination.current_page + 1)}>
                        Next
                    </button>
                )}
            </div>
        </div>
    );
}

export default AssetsList;
```

### Error Handling

API returns consistent error responses:

**Validation Error (422):**
```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

**Authentication Error (401):**
```json
{
    "message": "Unauthenticated."
}
```

**Authorization Error (403):**
```json
{
    "message": "This action is unauthorized."
}
```

**Not Found Error (404):**
```json
{
    "message": "No query results for model [App\\Models\\Asset] 999"
}
```

**Business Logic Error (422):**
```json
{
    "message": "Cannot delete an asset that is currently assigned. Please return it first."
}
```

### CORS Configuration

For React development on a different port, configure CORS in `config/cors.php`:

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], // React dev server
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### Testing API with cURL

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Get assets (with token)
curl http://localhost:8000/api/assets \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Create asset
curl -X POST http://localhost:8000/api/assets \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"name":"New Laptop","serial_number":"SN12345","category_id":1,"status":"available"}'

# Assign asset
curl -X POST http://localhost:8000/api/assets/1/assign \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"user_id":2}'
```

---

## Testing Strategy

### Test Structure

The application has 97 tests covering:
- **Authentication** (10 tests)
- **Asset Management** (25 tests)
- **Asset Assignment** (10 tests)
- **User Management** (17 tests)
- **Category Management** (14 tests)
- **Employee Assets** (8 tests)
- **Profile Management** (9 tests)
- **Dashboard** (3 tests)
- **Authorization** (9 tests)

### Testing Tools

- **Pest PHP** - Modern testing framework
- **Laravel RefreshDatabase** - Database reset between tests
- **Factories** - Generate test data
- **Storage Fake** - Mock file uploads

### Test Examples

**Feature Test:**

```php
test('admin can assign available asset to employee', function () {
    Event::fake(); // Mock events

    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);

    $this->actingAs($admin);

    $response = $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $employee->id,
    ]);

    // Assertions
    $response->assertRedirect("/assets/{$asset->id}");
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('assignments', [
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
    ]);

    $asset->refresh();
    expect($asset->status)->toBe('assigned');

    Event::assertDispatched(AssetAssigned::class);
});
```

**Authorization Test:**

```php
test('employee cannot access admin routes', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $routes = [
        '/dashboard',
        '/assets',
        '/users',
        '/categories',
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertStatus(403);
    }
});
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AssetManagementTest.php

# Run tests with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

---

## Setup & Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 5.7+ or MariaDB 10.3+
- Git

### Installation Steps

```bash
# 1. Clone repository
git clone <repository-url>
cd assetlara

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Create .env file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assetlara
DB_USERNAME=root
DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Create storage link
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. (Optional) Seed database
php artisan db:seed
```

### Development Setup

```bash
# Terminal 1: Start Laravel dev server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev

# Terminal 3: Start queue worker
php artisan queue:work

# Terminal 4: Start Reverb WebSocket server
php artisan reverb:start

# Or use the convenience script
composer dev
```

### Quick Setup Script

```bash
# All-in-one setup
composer setup
```

This runs:
1. `composer install`
2. `npm install`
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan storage:link`
6. `npm run build`

---

## Development Workflow

### Creating a New Feature

**Example: Adding a "Maintenance Mode" status for assets**

**Step 1: Update Database**

```bash
# Create migration
php artisan make:migration add_maintenance_status_to_assets
```

```php
// Migration file
public function up()
{
    Schema::table('assets', function (Blueprint $table) {
        // Update enum to include 'maintenance'
        DB::statement("ALTER TABLE assets MODIFY status ENUM('available', 'assigned', 'broken', 'maintenance') DEFAULT 'available'");
    });
}
```

```bash
php artisan migrate
```

**Step 2: Update Model**

No changes needed - Eloquent will automatically recognize the new enum value.

**Step 3: Update Form Requests**

```php
// StoreAssetRequest & UpdateAssetRequest
'status' => 'required|in:available,assigned,broken,maintenance',
```

**Step 4: Update Views**

```blade
<!-- assets/create.blade.php & edit.blade.php -->
<option value="maintenance">Under Maintenance</option>
```

```blade
<!-- Show status badge with maintenance color -->
@if($asset->status === 'maintenance')
    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
        Maintenance
    </span>
@endif
```

**Step 5: Write Tests**

```php
test('admin can create asset with maintenance status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $this->actingAs($admin);

    $response = $this->post('/assets', [
        'name' => 'Test Laptop',
        'serial_number' => 'SN12345',
        'category_id' => $category->id,
        'status' => 'maintenance',
    ]);

    $this->assertDatabaseHas('assets', [
        'serial_number' => 'SN12345',
        'status' => 'maintenance',
    ]);
});
```

**Step 6: Run Tests**

```bash
php artisan test
```

### Git Workflow

```bash
# 1. Create feature branch
git checkout -b feature/maintenance-status

# 2. Make changes and commit
git add .
git commit -m "Add maintenance status for assets"

# 3. Push to remote
git push origin feature/maintenance-status

# 4. Create pull request on GitHub/GitLab
```

---

## Code Examples & Patterns

### Pattern 1: Service Layer with Transactions

**Problem:** Controllers become bloated with business logic

**Solution:** Extract to service class

```php
// Bad - Logic in controller
public function assign(Request $request, Asset $asset)
{
    if ($asset->status !== 'available') {
        return redirect()->back()->with('error', 'Asset not available');
    }

    $assignment = Assignment::create([...]);
    $asset->update(['status' => 'assigned']);
    AssetAssigned::dispatch($assignment);

    return redirect()->back()->with('success', 'Asset assigned');
}

// Good - Logic in service
public function assign(AssignAssetRequest $request, Asset $asset)
{
    try {
        $this->assignmentService->assignAsset($asset, $request->user_id);
        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset assigned successfully!');
    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Assignment failed: ' . $e->getMessage());
    }
}
```

### Pattern 2: Form Request Validation

**Problem:** Validation logic clutters controllers

**Solution:** Use Form Request classes

```php
// Bad - Validation in controller
public function store(Request $request)
{
    $validated = $request->validate([
        'serial_number' => 'required|unique:assets',
        'name' => 'required|max:255',
        // ... more rules
    ]);

    // Custom messages
    $messages = [
        'serial_number.unique' => 'This serial number already exists',
    ];
}

// Good - Dedicated Form Request
class StoreAssetRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'serial_number' => 'required|unique:assets',
            'name' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'serial_number.unique' => 'This serial number already exists',
        ];
    }
}

// Controller
public function store(StoreAssetRequest $request)
{
    // $request is already validated!
    Asset::create($request->validated());
}
```

### Pattern 3: Policy-Based Authorization

**Problem:** Authorization checks scattered throughout code

**Solution:** Centralize in Policy classes

```php
// Bad - Manual checks everywhere
if (auth()->user()->role !== 'admin') {
    abort(403);
}

// Good - Policy + Resource Controller
class AssetController extends Controller
{
    public function __construct()
    {
        // Automatically checks policy for all actions
        $this->authorizeResource(Asset::class, 'asset');
    }
}

class AssetPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny();
    }
}
```

### Pattern 4: Eager Loading to Prevent N+1

**Problem:** N+1 query performance issue

**Solution:** Eager load relationships

```php
// Bad - N+1 problem
$assets = Asset::all();
foreach ($assets as $asset) {
    echo $asset->category->name; // Separate query for each!
}

// Good - Eager loading
$assets = Asset::with('category')->get();
foreach ($assets as $asset) {
    echo $asset->category->name; // Already loaded!
}

// Even better - Eager load in controller
public function index()
{
    $assets = Asset::with('category')
        ->orderBy('id', 'desc')
        ->paginate(10);

    return view('assets.index', compact('assets'));
}
```

### Pattern 5: Query Scopes for Reusability

**Problem:** Repeating same query logic

**Solution:** Use local scopes

```php
// Bad - Repeating query
$available = Asset::where('status', 'available')->get();
$available2 = Asset::where('status', 'available')->count();

// Good - Scope in model
class Asset extends Model
{
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}

// Usage
$available = Asset::available()->get();
$count = Asset::available()->count();
```

### Pattern 6: Flash Messages for User Feedback

**Problem:** User doesn't know if action succeeded

**Solution:** Use session flash messages

```php
// Controller
return redirect()->route('assets.index')
    ->with('success', 'Asset created successfully!');

// Layout (app.blade.php)
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
@endif
```

### Pattern 7: Database Transactions for Consistency

**Problem:** Partial updates leave inconsistent state

**Solution:** Wrap in transaction

```php
// Bad - If second update fails, first already succeeded
$assignment = Assignment::create([...]);
$asset->update(['status' => 'assigned']); // What if this fails?

// Good - All or nothing
DB::transaction(function () use ($asset, $userId) {
    $assignment = Assignment::create([...]);
    $asset->update(['status' => 'assigned']);
    AssetAssigned::dispatch($assignment);
    // If any fails, everything rolls back
});
```

---

## Common Pitfalls & Solutions

### Pitfall 1: Mass Assignment Vulnerability

**Problem:**
```php
// User can inject any field via request
User::create($request->all()); // DANGEROUS!
```

**Solution:**
```php
// Whitelist fillable fields in model
protected $fillable = ['name', 'email', 'password'];

// Or use validated() which only returns validated fields
User::create($request->validated());
```

### Pitfall 2: Not Using Database Transactions

**Problem:**
```php
$assignment = Assignment::create([...]);
$asset->update([...]); // If this fails, assignment already created!
```

**Solution:**
```php
DB::transaction(function () {
    $assignment = Assignment::create([...]);
    $asset->update([...]);
});
```

### Pitfall 3: N+1 Query Problem

**Problem:**
```php
$assets = Asset::all();
foreach ($assets as $asset) {
    echo $asset->category->name; // N+1 queries!
}
```

**Solution:**
```php
$assets = Asset::with('category')->all();
```

### Pitfall 4: Not Validating File Uploads

**Problem:**
```php
$path = $request->file('image')->store('assets'); // No validation!
```

**Solution:**
```php
$request->validate([
    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
]);
```

### Pitfall 5: Exposing Sensitive Data

**Problem:**
```php
return response()->json($user); // Includes password hash!
```

**Solution:**
```php
// Hide in model
protected $hidden = ['password', 'remember_token'];

// Or use API Resources
return new UserResource($user);
```

---

## Performance Optimization Tips

### 1. Use Eager Loading
```php
// Before
$assets = Asset::all(); // 1 query
foreach ($assets as $asset) {
    $asset->category->name; // N queries
}

// After
$assets = Asset::with('category')->all(); // 2 queries total
```

### 2. Use Pagination
```php
// Instead of
$assets = Asset::all();

// Use
$assets = Asset::paginate(10);
```

### 3. Cache Expensive Queries
```php
$categories = Cache::remember('categories', 3600, function () {
    return Category::withCount('assets')->get();
});
```

### 4. Use Database Indexing
```php
// In migration
$table->index('serial_number');
$table->index('status');
$table->index(['category_id', 'status']);
```

### 5. Optimize Images
```php
// Use intervention/image package
$image = Image::make($request->file('image'))
    ->resize(800, null, function ($constraint) {
        $constraint->aspectRatio();
    })
    ->save(storage_path('app/public/assets/' . $filename));
```

---

## Security Best Practices

### 1. Always Use CSRF Protection
```blade
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

### 2. Use Password Hashing
```php
// Never store plain passwords
'password' => Hash::make($request->password),

// Verify passwords
Hash::check($plain, $hashed);
```

### 3. Validate All User Input
```php
// Use Form Requests
public function store(StoreAssetRequest $request) {
    // $request is already validated
}
```

### 4. Implement Authorization
```php
// Use Policies
$this->authorize('update', $asset);

// Or Gates
if (Gate::denies('update-asset', $asset)) {
    abort(403);
}
```

### 5. Prevent SQL Injection
```php
// Always use parameter binding (Eloquent does this automatically)
Asset::where('status', $status)->get(); // SAFE

// NEVER do this
DB::select("SELECT * FROM assets WHERE status = '$status'"); // DANGEROUS
```

### 6. Protect Against XSS
```blade
<!-- Blade automatically escapes -->
{{ $user->name }} <!-- SAFE -->

<!-- Use {!! !!} only for trusted HTML -->
{!! $trustedHtml !!}
```

### 7. Use Rate Limiting
```php
Route::post('/login', [AuthController::class, 'store'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

---

## Deployment Checklist

### 1. Environment Configuration
```bash
# Set app environment to production
APP_ENV=production
APP_DEBUG=false

# Generate new app key
php artisan key:generate

# Configure production database
DB_DATABASE=production_db
DB_USERNAME=prod_user
DB_PASSWORD=strong_password
```

### 2. Optimize Application
```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 3. Build Assets
```bash
npm run build
```

### 4. Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
```

### 5. Run Migrations
```bash
php artisan migrate --force
```

### 6. Setup Queue Worker
```bash
# Use Supervisor to keep queue worker running
sudo supervisorctl start laravel-worker:*
```

### 7. Setup Task Scheduler
```bash
# Add to crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Troubleshooting Guide

### Issue: "Class not found" Error
**Solution:**
```bash
composer dump-autoload
```

### Issue: "Route not found" Error
**Solution:**
```bash
php artisan route:cache
php artisan route:list # Verify route exists
```

### Issue: "View not found" Error
**Solution:**
```bash
php artisan view:clear
php artisan view:cache
```

### Issue: "Permission denied" for storage
**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Issue: WebSocket not connecting
**Solution:**
```bash
# Check Reverb is running
php artisan reverb:start

# Check .env configuration
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-secret
REVERB_HOST=localhost
REVERB_PORT=8080
```

### Issue: Tests failing randomly
**Solution:**
```php
// tests/Pest.php
pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class) // Add this
    ->in('Feature');
```

---

## Additional Resources

### Laravel Documentation
- Official Docs: https://laravel.com/docs
- Laracasts: https://laracasts.com
- Laravel News: https://laravel-news.com

### Related Technologies
- Tailwind CSS: https://tailwindcss.com/docs
- Laravel Reverb: https://reverb.laravel.com/docs
- Pest PHP: https://pestphp.com/docs
- Laravel Echo: https://laravel.com/docs/broadcasting#client-side-installation

### Best Practices
- Laravel Best Practices: https://github.com/alexeymezenin/laravel-best-practices
- PHP The Right Way: https://phptherightway.com

---

## Conclusion

This Laravel Asset Tracking System demonstrates professional-grade development practices including:

- Clean MVC architecture
- Service layer pattern
- Policy-based authorization
- Database transactions
- Real-time broadcasting
- Comprehensive testing
- Security best practices

Understanding this codebase will give you a solid foundation in Laravel development and prepare you for building production-ready applications.

**Happy Learning!**
