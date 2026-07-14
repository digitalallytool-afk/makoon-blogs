<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Run the roles and permissions seeder
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('super admin can access dashboard and users index', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->get(route('dashboard'));
    $response->assertStatus(200);

    $response = $this->actingAs($superAdmin)->get(route('users.index'));
    $response->assertStatus(200);
});

test('unauthorized admin user is redirected or blocked when accessing dashboard without view-dashboard permission', function () {
    // Create an admin user without any permissions
    $role = Role::where('slug', 'admin')->first();
    $user = User::factory()->create();
    $user->roles()->attach($role);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertStatus(403); // Forbidden since they don't have view-dashboard permission
});

test('authorized admin user can access specific routes they have permission for', function () {
    $role = Role::where('slug', 'admin')->first();
    $user = User::factory()->create();
    $user->roles()->attach($role);

    // Give view-dashboard and view-posts permissions directly
    $dashboardPerm = Permission::where('slug', 'view-dashboard')->first();
    $postsPerm = Permission::where('slug', 'view-posts')->first();

    $user->permissions()->attach([$dashboardPerm->id, $postsPerm->id]);
    $user->load('permissions');

    // Access dashboard (should succeed)
    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertStatus(200);

    // Access allpost (should succeed)
    $response = $this->actingAs($user)->get(route('allPost'));
    $response->assertStatus(200);

    // Access categories (should fail - forbidden)
    $response = $this->actingAs($user)->get(route('categories'));
    $response->assertStatus(403);
});

test('super admin can configure user role and permissions', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $role = Role::where('slug', 'admin')->first();
    $user = User::factory()->create();
    $user->roles()->attach($role);

    // Super admin updates user permissions to include create-posts and manage-media
    $response = $this->actingAs($superAdmin)->post(route('users.permissions', $user->id), [
        'role' => 'admin',
        'permissions' => ['view-dashboard', 'view-posts', 'create-posts', 'manage-media'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Load fresh user data
    $user = $user->fresh();

    expect($user->hasPermission('create-posts'))->toBeTrue();
    expect($user->hasPermission('manage-media'))->toBeTrue();
    expect($user->hasPermission('manage-categories'))->toBeFalse(); // not granted
});

test('super admin can create a new user from the dashboard', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->post(route('users.store'), [
        'name' => 'New Dash User',
        'email' => 'dashuser@example.com',
        'password' => 'password123',
        'role' => 'admin',
        'permissions' => ['view-dashboard', 'view-posts', 'create-posts'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user = User::where('email', 'dashuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasPermission('view-dashboard'))->toBeTrue();
    expect($user->hasPermission('view-posts'))->toBeTrue();
    expect($user->hasPermission('create-posts'))->toBeTrue();
    expect($user->hasPermission('edit-posts'))->toBeFalse();
});

test('non-super admin users cannot create a new user from the dashboard', function () {
    $adminUser = User::where('email', 'editor@example.com')->first();

    $response = $this->actingAs($adminUser)->post(route('users.store'), [
        'name' => 'Hack User',
        'email' => 'hackuser@example.com',
        'password' => 'password123',
        'role' => 'super-admin',
    ]);

    $response->assertStatus(403);
    expect(User::where('email', 'hackuser@example.com')->exists())->toBeFalse();
});

test('super admin can delete other users but not self or primary super admin', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create a dummy user to delete
    $dummy = User::factory()->create();

    // 1. Delete dummy user
    $response = $this->actingAs($superAdmin)->delete(route('users.destroy', $dummy->id));
    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect(User::find($dummy->id))->toBeNull();

    // 2. Prevent self-deletion
    $response = $this->actingAs($superAdmin)->delete(route('users.destroy', $superAdmin->id));
    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(User::find($superAdmin->id))->not->toBeNull();

    // 3. Prevent primary super admin (ID 1) deletion
    $user1 = User::find(1) ?? User::factory()->create(['id' => 1]);
    $response = $this->actingAs($superAdmin)->delete(route('users.destroy', 1));
    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(User::find(1))->not->toBeNull();
});

test('non-super admin users cannot delete users', function () {
    $adminUser = User::where('email', 'editor@example.com')->first();
    $dummy = User::factory()->create();

    $response = $this->actingAs($adminUser)->delete(route('users.destroy', $dummy->id));
    $response->assertStatus(403);
    expect(User::find($dummy->id))->not->toBeNull();
});

test('super admin can update user name, email, password, and status', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->actingAs($superAdmin)->post(route('users.permissions', $user->id), [
        'name' => 'Updated Name',
        'email' => 'updatedemail@example.com',
        'password' => 'newpassword123',
        'role' => 'admin',
        'is_active_submitted' => '1',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user = $user->fresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updatedemail@example.com');
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
    expect($user->is_active)->toBeFalse();
});

test('inactive user cannot log in', function () {
    $user = User::factory()->create([
        'email' => 'inactiveuser@example.com',
        'password' => bcrypt('password123'),
        'is_active' => false,
    ]);

    $response = $this->post('/login', [
        'email' => 'inactiveuser@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
