<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createDemoAuthUser(): User
{
    $role = Role::create([
        'name' => 'Super Admin',
        'slug' => 'super_admin',
    ]);

    return User::factory()->create([
        'role_id' => $role->id,
        'name' => 'Super Admin',
        'email' => 'superadmin@inventory.test',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]);
}

test('user can login with valid credentials', function () {
    createDemoAuthUser();

    $response = $this->postJson('/api/auth/login', [
        'email' => 'superadmin@inventory.test',
        'password' => 'password',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Login berhasil')
        ->assertJsonPath('data.user.email', 'superadmin@inventory.test')
        ->assertJsonPath('data.user.role.slug', 'super_admin')
        ->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
});

test('user cannot login with invalid credentials', function () {
    createDemoAuthUser();

    $response = $this->postJson('/api/auth/login', [
        'email' => 'superadmin@inventory.test',
        'password' => 'wrong-password',
    ]);

    $response
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Email atau password tidak sesuai');
});

test('authenticated user can access me endpoint', function () {
    $user = createDemoAuthUser();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this
        ->withToken($token)
        ->getJson('/api/auth/me');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.user.email', 'superadmin@inventory.test')
        ->assertJsonPath('data.user.role.slug', 'super_admin');
});
