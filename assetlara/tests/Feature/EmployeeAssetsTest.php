<?php

use App\Models\User;
use App\Models\Asset;
use App\Models\Assignment;

test('employee can view their assets page', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->get('/my-assets');
    $response->assertStatus(200);
    $response->assertViewIs('assets.my-assets');
});

test('admin is redirected from my-assets to dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/my-assets');
    $response->assertRedirect('/dashboard');
});

test('employee sees only their assigned assets', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $otherEmployee = User::factory()->create(['role' => 'employee']);

    $asset1 = Asset::factory()->create(['status' => 'assigned']);
    $asset2 = Asset::factory()->create(['status' => 'assigned']);
    $asset3 = Asset::factory()->create(['status' => 'assigned']);

    Assignment::create([
        'asset_id' => $asset1->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    Assignment::create([
        'asset_id' => $asset2->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    Assignment::create([
        'asset_id' => $asset3->id,
        'user_id' => $otherEmployee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($employee);

    $response = $this->get('/my-assets');
    $response->assertStatus(200);
    $response->assertViewHas('myAssets', function ($assets) use ($asset1, $asset2, $asset3) {
        return $assets->count() === 2 &&
               $assets->contains('id', $asset1->id) &&
               $assets->contains('id', $asset2->id) &&
               !$assets->contains('id', $asset3->id);
    });
});

test('employee does not see returned assets', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    $asset1 = Asset::factory()->create(['status' => 'assigned']);
    $asset2 = Asset::factory()->create(['status' => 'available']);

    Assignment::create([
        'asset_id' => $asset1->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now()->subDays(5),
    ]);

    Assignment::create([
        'asset_id' => $asset2->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now()->subDays(10),
        'returned_at' => now()->subDays(2),
    ]);

    $this->actingAs($employee);

    $response = $this->get('/my-assets');
    $response->assertStatus(200);
    $response->assertViewHas('myAssets', function ($assets) use ($asset1, $asset2) {
        return $assets->count() === 1 &&
               $assets->contains('id', $asset1->id) &&
               !$assets->contains('id', $asset2->id);
    });
});

test('employee can view details of assigned asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $category = \App\Models\Category::factory()->create();
    $asset = Asset::factory()->create(['status' => 'assigned', 'category_id' => $category->id]);

    Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($employee);

    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(200);
    $response->assertViewIs('assets.show');
});

test('employee cannot view details of unassigned asset', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);

    $this->actingAs($employee);

    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(403);
});

test('employee cannot view details of asset assigned to others', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $otherEmployee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'assigned']);

    Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $otherEmployee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($employee);

    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(403);
});

test('guest cannot view my assets page', function () {
    $response = $this->get('/my-assets');
    $response->assertRedirect('/login');
});
