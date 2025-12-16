<?php

use App\Models\User;
use App\Models\Asset;
use App\Models\Assignment;

test('guest is redirected to login for protected routes', function () {
    $routes = [
        '/dashboard',
        '/assets',
        '/assets/create',
        '/users',
        '/categories',
        '/my-assets',
        '/profile',
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertRedirect('/login');
    }
});

test('employee cannot access admin routes', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $routes = [
        '/dashboard',
        '/assets',
        '/assets/create',
        '/users',
        '/categories',
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertStatus(403);
    }
});

test('admin can access all admin routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $routes = [
        '/dashboard' => 200,
        '/assets' => 200,
        '/assets/create' => 200,
        '/users' => 200,
        '/categories' => 200,
        '/profile' => 200,
    ];

    foreach ($routes as $route => $expectedStatus) {
        $response = $this->get($route);
        $response->assertStatus($expectedStatus);
    }

    // Admin should be redirected from my-assets to dashboard
    $response = $this->get('/my-assets');
    $response->assertRedirect('/dashboard');
});

test('admin is redirected from employee-only routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    // Admin should be redirected from my-assets to dashboard
    $response = $this->get('/my-assets');
    $response->assertRedirect('/dashboard');

    // But admin can still access profile
    $response = $this->get('/profile');
    $response->assertStatus(200);
});

test('employee can access employee routes', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $routes = [
        '/my-assets' => 200,
        '/profile' => 200,
    ];

    foreach ($routes as $route => $expectedStatus) {
        $response = $this->get($route);
        $response->assertStatus($expectedStatus);
    }
});

test('policy allows admin to bypass all checks', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = \App\Models\Category::factory()->create();
    $asset = Asset::factory()->create(['category_id' => $category->id, 'status' => 'available']);
    $this->actingAs($admin);

    // Admin should be able to view any asset regardless of assignment status
    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(200);
});

test('policy allows employee to view only their assigned assets', function () {
    $adminUser = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $category = \App\Models\Category::factory()->create();
    $asset = Asset::factory()->create(['status' => 'assigned', 'category_id' => $category->id]);

    Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $adminUser->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($employee);

    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(200);
});

test('policy denies employee from viewing unassigned assets', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);

    $this->actingAs($employee);

    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(403);
});

test('authenticated users can access api endpoints', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee, 'sanctum');

    $response = $this->getJson('/api/auth/user');
    $response->assertStatus(200);
    $response->assertJson([
        'user' => [
            'email' => $employee->email,
        ],
    ]);
});
