<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('users can view login page', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

test('users can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'is_active' => true,
        'role' => 'admin'
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('users cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('inactive users cannot login', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'is_active' => false,
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('users can view registration page', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
    $response->assertViewIs('auth.register');
});

test('users can register with valid data', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/my-assets');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'role' => 'employee',
    ]);
});

test('users cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('admin redirects to dashboard after login', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
});

test('employee redirects to my assets after login', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $employee->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/my-assets');
});

test('authenticated users can logout', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
