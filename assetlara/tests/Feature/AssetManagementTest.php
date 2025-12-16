<?php

use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('admin can view assets index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/assets');
    $response->assertStatus(200);
    $response->assertViewIs('assets.index');
});

test('employee cannot view assets index', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->get('/assets');
    $response->assertStatus(403);
});

test('admin can view create asset form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/assets/create');
    $response->assertStatus(200);
    $response->assertViewIs('assets.create');
});

test('admin can create asset with valid data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $this->actingAs($admin);

    $response = $this->post('/assets', [
        'name' => 'Test Laptop',
        'serial_number' => 'SN12345',
        'category_id' => $category->id,
        'status' => 'available',
    ]);

    $response->assertRedirect('/assets');
    $this->assertDatabaseHas('assets', [
        'name' => 'Test Laptop',
        'serial_number' => 'SN12345',
        'status' => 'available',
    ]);
});

test('admin can create asset with image', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $this->actingAs($admin);

    $file = UploadedFile::fake()->image('laptop.jpg');

    $response = $this->post('/assets', [
        'name' => 'Test Laptop',
        'serial_number' => 'SN12345',
        'category_id' => $category->id,
        'status' => 'available',
        'image' => $file,
    ]);

    $response->assertRedirect('/assets');
    $asset = Asset::where('serial_number', 'SN12345')->first();
    expect($asset->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($asset->image_path);
});

test('cannot create asset with duplicate serial number', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    Asset::factory()->create(['serial_number' => 'SN12345']);
    $this->actingAs($admin);

    $response = $this->post('/assets', [
        'name' => 'Test Laptop',
        'serial_number' => 'SN12345',
        'category_id' => $category->id,
        'status' => 'available',
    ]);

    $response->assertSessionHasErrors('serial_number');
});

test('admin can view asset details', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create();
    $this->actingAs($admin);

    $response = $this->get("/assets/{$asset->id}");
    $response->assertStatus(200);
    $response->assertViewIs('assets.show');
    $response->assertViewHas('asset');
});

test('admin can update asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $asset = Asset::factory()->create();
    $this->actingAs($admin);

    $response = $this->put("/assets/{$asset->id}", [
        'name' => 'Updated Name',
        'serial_number' => $asset->serial_number,
        'category_id' => $category->id,
        'status' => 'available',
    ]);

    $response->assertRedirect("/assets/{$asset->id}");
    $this->assertDatabaseHas('assets', [
        'id' => $asset->id,
        'name' => 'Updated Name',
    ]);
});

test('admin can update asset image', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $asset = Asset::factory()->create(['image_path' => 'assets/old.jpg']);
    $this->actingAs($admin);

    Storage::disk('public')->put('assets/old.jpg', 'old content');

    $newFile = UploadedFile::fake()->image('new.jpg');

    $response = $this->put("/assets/{$asset->id}", [
        'name' => $asset->name,
        'serial_number' => $asset->serial_number,
        'category_id' => $category->id,
        'status' => 'available',
        'image' => $newFile,
    ]);

    $response->assertRedirect("/assets/{$asset->id}");
    $asset->refresh();
    expect($asset->image_path)->not->toBe('assets/old.jpg');
    Storage::disk('public')->assertExists($asset->image_path);
    Storage::disk('public')->assertMissing('assets/old.jpg');
});

test('admin can delete available asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($admin);

    $response = $this->delete("/assets/{$asset->id}");

    $response->assertRedirect('/assets');
    $this->assertSoftDeleted('assets', ['id' => $asset->id]);
});

test('admin cannot delete assigned asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create(['status' => 'assigned']);
    $this->actingAs($admin);

    $response = $this->delete("/assets/{$asset->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('assets', ['id' => $asset->id, 'deleted_at' => null]);
});

test('employee cannot create asset', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $category = Category::factory()->create();
    $this->actingAs($employee);

    $response = $this->post('/assets', [
        'name' => 'Test Laptop',
        'serial_number' => 'SN12345',
        'category_id' => $category->id,
        'status' => 'available',
    ]);

    $response->assertStatus(403);
});

test('employee cannot update asset', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create();
    $this->actingAs($employee);

    $response = $this->put("/assets/{$asset->id}", [
        'name' => 'Updated Name',
        'serial_number' => $asset->serial_number,
        'category_id' => $asset->category_id,
        'status' => 'available',
    ]);

    $response->assertStatus(403);
});

test('employee cannot delete asset', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create();
    $this->actingAs($employee);

    $response = $this->delete("/assets/{$asset->id}");

    $response->assertStatus(403);
});

test('assets are paginated on index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Asset::factory()->count(15)->create();
    $this->actingAs($admin);

    $response = $this->get('/assets');
    $response->assertStatus(200);
    $response->assertViewHas('assets', function ($assets) {
        return $assets->count() === 10; // Per page
    });
});
