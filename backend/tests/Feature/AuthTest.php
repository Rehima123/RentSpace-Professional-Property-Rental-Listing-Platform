<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */
    public function test_tenant_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Meron Alemu',
            'email' => 'meron@rentspace.et',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tenant',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'tenant')
            ->assertJsonPath('data.email', 'meron@rentspace.et');

        $this->assertDatabaseHas('users', [
            'email' => 'meron@rentspace.et',
            'role' => 'tenant',
        ]);
    }

    public function test_owner_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Abebe Kebede',
            'email' => 'abebe@rentspace.et',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'owner',
            'phone' => '+251911000001',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'owner');
    }

    public function test_cannot_register_as_admin(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Hacker',
            'email' => 'hacker@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tenant',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch',
            'role' => 'tenant',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@test.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Duplicate',
            'email' => 'taken@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'tenant',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@rentspace.et',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@rentspace.et',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'test@rentspace.et');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'valid@rentspace.et',
            'password' => bcrypt('correct'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'valid@rentspace.et',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_fails_with_non_existent_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nobody@rentspace.et',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout & Current User
    |--------------------------------------------------------------------------
    */
    public function test_authenticated_user_can_get_their_profile(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonMissing(['password']);
    }

    public function test_unauthenticated_request_to_user_returns_401(): void
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Logged out.');
    }

    /*
    |--------------------------------------------------------------------------
    | Role Middleware
    |--------------------------------------------------------------------------
    */
    public function test_password_is_not_exposed_in_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertJsonMissing(['password', 'remember_token']);
    }
}
