<?php

use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and receive the super-admin role by default', function () {
    $response = $this->post('/register', [
        'name' => 'Test Guest User',
        'email' => 'guest_test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'guest_test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('super-admin'))->toBeTrue();
    expect($user->hasPermission('view-dashboard'))->toBeTrue();
});
