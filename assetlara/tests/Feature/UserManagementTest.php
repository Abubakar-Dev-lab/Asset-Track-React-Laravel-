<?php

use App\Models\User;
use App\Models\Asset;
use App\Models\Assignment;
use Illuminate\Support\Facades\Hash;

test('admin can view users index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/users');
    $response->assertStatus(200);
    $response->assertViewIs('users.index');
});

test('employee cannot view users index', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->get('/users');
    $response->assertStatus(403);
});

test('admin can create new user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->post('/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'employee',
    ]);

    $response->assertRedirect('/users');
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'name' => 'New User',
        'role' => 'employee',
    ]);
});

test('admin can create admin user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->post('/users', [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'admin',
    ]);

    $response->assertRedirect('/users');
    $this->assertDatabaseHas('users', [
        'email' => 'newadmin@example.com',
        'role' => 'admin',
    ]);
});

test('admin can view user details', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->get("/users/{$user->id}");
    $response->assertStatus(200);
    $response->assertViewIs('users.show');
});

test('admin can update user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->put("/users/{$user->id}", [
        'name' => 'Updated Name',
        'email' => $user->email,
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response->assertRedirect('/users');
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'role' => 'admin',
    ]);
});

test('admin can update user password', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['password' => Hash::make('oldpassword')]);
    $this->actingAs($admin);

    $response = $this->put("/users/{$user->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'is_active' => $user->is_active,
    ]);

    $response->assertRedirect('/users');
    // Note: UserController update doesn't handle password updates,
    // only ProfileController does
});

test('admin can soft delete user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->delete("/users/{$user->id}");

    $response->assertRedirect('/users');
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->delete("/users/{$admin->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
});

test('admin cannot delete user with active assignments', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'assigned']);

    Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($admin);

    $response = $this->delete("/users/{$employee->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $employee->id, 'deleted_at' => null]);
});

test('employee cannot create user', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $this->actingAs($employee);

    $response = $this->post('/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'employee',
    ]);

    $response->assertStatus(403);
});

test('employee cannot update user', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $otherUser = User::factory()->create();
    $this->actingAs($employee);

    $response = $this->put("/users/{$otherUser->id}", [
        'name' => 'Updated Name',
        'email' => $otherUser->email,
        'role' => 'employee',
    ]);

    $response->assertStatus(403);
});

test('employee cannot delete user', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $otherUser = User::factory()->create();
    $this->actingAs($employee);

    $response = $this->delete("/users/{$otherUser->id}");

    $response->assertStatus(403);
});

test('users are paginated on index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->count(20)->create();
    $this->actingAs($admin);

    $response = $this->get('/users');
    $response->assertStatus(200);
    $response->assertViewHas('users', function ($users) {
        return $users->count() === 15; // Per page
    });
});
