<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can view dashboard as admin', function () {
    $this->seed();
    $user = User::first(); // seeded user

    actingAs($user)
        ->get('/admin')
        ->assertRedirect('/admin/login')
        ->assertStatus(302);
});

it('can view dashboard after login', function () {
    $this->seed();
    $user = User::first();

    actingAs($user)
        ->get('/admin/dashboard')
        ->assertStatus(200);
});
