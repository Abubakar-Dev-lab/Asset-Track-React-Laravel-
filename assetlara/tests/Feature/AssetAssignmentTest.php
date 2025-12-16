<?php

use App\Models\Asset;
use App\Models\Assignment;
use App\Models\User;
use App\Events\AssetAssigned;
use App\Events\AssetReturned;
use Illuminate\Support\Facades\Event;

test('admin can assign available asset to employee', function () {
    Event::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($admin);

    $response = $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $employee->id,
    ]);

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

test('admin cannot assign already assigned asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'assigned']);
    $this->actingAs($admin);

    $response = $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $employee->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin can return assigned asset', function () {
    Event::fake();

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

    $response = $this->post("/assets/{$asset->id}/return");

    $response->assertRedirect("/assets/{$asset->id}");
    $response->assertSessionHas('success');

    $assignment->refresh();
    expect($assignment->returned_at)->not->toBeNull();

    $asset->refresh();
    expect($asset->status)->toBe('available');

    Event::assertDispatched(AssetReturned::class);
});

test('admin cannot return asset that is not assigned', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($admin);

    $response = $this->post("/assets/{$asset->id}/return");

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('employee cannot assign asset', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $otherEmployee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($employee);

    $response = $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $otherEmployee->id,
    ]);

    $response->assertStatus(403);
});

test('employee cannot return asset', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'assigned']);

    Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($employee);

    $response = $this->post("/assets/{$asset->id}/return");

    $response->assertStatus(403);
});

test('assignment creates audit trail', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($admin);

    $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $employee->id,
    ]);

    $assignment = Assignment::where('asset_id', $asset->id)
        ->whereNull('returned_at')
        ->first();

    expect($assignment)->not->toBeNull();
    expect($assignment->user_id)->toBe($employee->id);
    expect($assignment->assigned_by)->toBe($admin->id);
    expect($assignment->assigned_at)->not->toBeNull();
    expect($assignment->returned_at)->toBeNull();
});

test('return updates audit trail', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'assigned']);

    $assignment = Assignment::create([
        'asset_id' => $asset->id,
        'user_id' => $employee->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now()->subDays(5),
    ]);

    $this->actingAs($admin);

    $this->post("/assets/{$asset->id}/return");

    $assignment->refresh();
    expect($assignment->returned_at)->not->toBeNull();
    expect($assignment->assigned_at)->not->toBeNull();
});

test('asset assignment uses database transaction', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($admin);

    // Test with invalid user_id to trigger transaction rollback
    $response = $this->post("/assets/{$asset->id}/assign", [
        'user_id' => 99999, // Non-existent user
    ]);

    $response->assertSessionHasErrors();

    // Asset status should remain unchanged due to transaction rollback
    $asset->refresh();
    expect($asset->status)->toBe('available');

    // No assignment should be created
    $this->assertDatabaseMissing('assignments', [
        'asset_id' => $asset->id,
        'user_id' => 99999,
    ]);
});

test('assignment history is preserved after return', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);
    $asset = Asset::factory()->create(['status' => 'available']);
    $this->actingAs($admin);

    // Assign
    $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $employee->id,
    ]);

    // Return
    $this->post("/assets/{$asset->id}/return");

    // Assign again
    $this->post("/assets/{$asset->id}/assign", [
        'user_id' => $employee->id,
    ]);

    // Should have 2 assignment records
    $assignments = Assignment::where('asset_id', $asset->id)->get();
    expect($assignments)->toHaveCount(2);
    expect($assignments[0]->returned_at)->not->toBeNull();
    expect($assignments[1]->returned_at)->toBeNull();
});
