<?php

use App\Models\User;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Assignment;

test('admin can view dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
    $response->assertViewIs('dashboard');
});

test('employee cannot view dashboard', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->get('/dashboard');
    $response->assertStatus(403);
});

test('guest cannot view dashboard', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('dashboard shows statistics', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create shared categories for assets to avoid factory auto-creating categories
    $category = Category::factory()->create();

    // Create test data using the same category
    Asset::factory()->count(10)->create(['status' => 'available', 'category_id' => $category->id]);
    Asset::factory()->count(5)->create(['status' => 'assigned', 'category_id' => $category->id]);
    Asset::factory()->count(2)->create(['status' => 'broken', 'category_id' => $category->id]);
    User::factory()->count(8)->create(['role' => 'employee']);
    Category::factory()->count(3)->create(); // Create 3 more categories (total 4)

    $this->actingAs($admin);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
    $response->assertViewHas('totalAssets', 17);
    $response->assertViewHas('availableAssets', 10);
    $response->assertViewHas('assignedAssets', 5);
    $response->assertViewHas('totalEmployees', 8);
    $response->assertViewHas('totalCategories', 4);
});

test('dashboard shows recent assignments', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'assigned']);

    $assignment = Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($admin);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
    $response->assertViewHas('recentAssignments', function ($assignments) use ($assignment) {
        return $assignments->contains('id', $assignment->id);
    });
});

test('dashboard limits recent assignments to 5', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    // Create 10 assignments
    for ($i = 0; $i < 10; $i++) {
        $asset = Asset::factory()->create(['status' => 'assigned']);
        Assignment::create([
            'asset_id' => $asset->id,
            'user_id' => $employee->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now()->subDays($i),
        ]);
    }

    $this->actingAs($admin);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
    $response->assertViewHas('recentAssignments', function ($assignments) {
        return $assignments->count() === 5;
    });
});
