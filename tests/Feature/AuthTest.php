<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '11999999999',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'phone'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_cannot_register_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('user');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['title', 'status', 'detail']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_authenticated_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }
}
