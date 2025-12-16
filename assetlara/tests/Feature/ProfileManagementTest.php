<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view profile edit page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/profile');
    $response->assertStatus(200);
    $response->assertViewIs('profile.edit');
});

test('user can update their name', function () {
    $user = User::factory()->create(['name' => 'Old Name']);
    $this->actingAs($user);

    $response = $this->put('/profile', [
        'name' => 'New Name',
        'email' => $user->email,
    ]);

    $response->assertRedirect('/profile');
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
    ]);
});

test('user can update their email', function () {
    $user = User::factory()->create(['email' => 'old@example.com']);
    $this->actingAs($user);

    $response = $this->put('/profile', [
        'name' => $user->name,
        'email' => 'new@example.com',
    ]);

    $response->assertRedirect('/profile');
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'new@example.com',
    ]);
});

test('user can update their password', function () {
    $user = User::factory()->create(['password' => Hash::make('oldpassword')]);
    $this->actingAs($user);

    $response = $this->put('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'current_password' => 'oldpassword',
        'new_password' => 'newpassword123',
        'new_password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect('/profile');
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

test('user cannot update email to existing email', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create(['email' => 'user@example.com']);
    $this->actingAs($user);

    $response = $this->put('/profile', [
        'name' => $user->name,
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors('email');
});

test('password must be confirmed', function () {
    $user = User::factory()->create(['password' => Hash::make('oldpassword')]);
    $this->actingAs($user);

    $response = $this->put('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'current_password' => 'oldpassword',
        'new_password' => 'newpassword123',
        'new_password_confirmation' => 'differentpassword',
    ]);

    $response->assertSessionHasErrors('new_password');
});

test('password must be at least 8 characters', function () {
    $user = User::factory()->create(['password' => Hash::make('oldpassword')]);
    $this->actingAs($user);

    $response = $this->put('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'current_password' => 'oldpassword',
        'new_password' => 'short',
        'new_password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('new_password');
});

test('guest cannot view profile page', function () {
    $response = $this->get('/profile');
    $response->assertRedirect('/login');
});

test('guest cannot update profile', function () {
    $response = $this->put('/profile', [
        'name' => 'Test Name',
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect('/login');
});
