<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('can login to admin panel', function () {
    $this->seed();
    $user = User::first();

    // Try to login with default password
    $response = post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Should redirect after successful login
    $response->assertRedirect();
});

it('can access dashboard after login', function () {
    $this->seed();
    $user = User::first();

    // Login
    post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Now try to access dashboard
    $response = get('/admin/dashboard');
    $response->assertStatus(200);
});
