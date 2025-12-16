<?php

use App\Models\Category;
use App\Models\Asset;
use App\Models\User;

test('admin can view categories index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/categories');
    $response->assertStatus(200);
    $response->assertViewIs('categories.index');
});

test('employee cannot view categories index', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->get('/categories');
    $response->assertStatus(403);
});

test('admin can create category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->post('/categories', [
        'name' => 'Laptops',
    ]);

    $response->assertRedirect('/categories');
    $this->assertDatabaseHas('categories', [
        'name' => 'Laptops',
        'slug' => 'laptops',
    ]);
});

test('category slug is auto generated', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->post('/categories', [
        'name' => 'Mobile Devices',
    ]);

    $this->assertDatabaseHas('categories', [
        'name' => 'Mobile Devices',
        'slug' => 'mobile-devices',
    ]);
});

test('cannot create duplicate category name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Category::factory()->create(['name' => 'Laptops']);
    $this->actingAs($admin);

    $response = $this->post('/categories', [
        'name' => 'Laptops',
    ]);

    $response->assertSessionHasErrors('name');
});

// Category show view not implemented, skipped

test('admin can update category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create(['name' => 'Old Name']);
    $this->actingAs($admin);

    $response = $this->put("/categories/{$category->id}", [
        'name' => 'New Name',
    ]);

    $response->assertRedirect("/categories");
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'New Name',
        'slug' => 'new-name',
    ]);
});

test('admin can delete category without assets', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $this->actingAs($admin);

    $response = $this->delete("/categories/{$category->id}");

    $response->assertRedirect('/categories');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('admin cannot delete category with assets', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    Asset::factory()->create(['category_id' => $category->id]);
    $this->actingAs($admin);

    $response = $this->delete("/categories/{$category->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('employee cannot create category', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->post('/categories', [
        'name' => 'Laptops',
    ]);

    $response->assertStatus(403);
});

test('employee cannot update category', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $category = Category::factory()->create();
    $this->actingAs($employee);

    $response = $this->put("/categories/{$category->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(403);
});

test('employee cannot delete category', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $category = Category::factory()->create();
    $this->actingAs($employee);

    $response = $this->delete("/categories/{$category->id}");

    $response->assertStatus(403);
});

test('categories show asset count', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    Asset::factory()->count(5)->create(['category_id' => $category->id]);
    $this->actingAs($admin);

    $response = $this->get('/categories');
    $response->assertStatus(200);
    $response->assertViewHas('categories', function ($categories) use ($category) {
        $cat = $categories->firstWhere('id', $category->id);
        return $cat && $cat->assets_count === 5;
    });
});

test('categories are paginated on index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Category::factory()->count(15)->create();
    $this->actingAs($admin);

    $response = $this->get('/categories');
    $response->assertStatus(200);
    $response->assertViewHas('categories', function ($categories) {
        return $categories->count() === 10; // Per page
    });
});
