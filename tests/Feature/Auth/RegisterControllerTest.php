<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'created_at',
                            'updated_at'
                        ],
                        'token'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente'
                ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);

        $this->assertNotNull($response->json('data.token'));
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'email'
                    ]
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Error de validación'
                ]);
    }

    public function test_user_cannot_register_with_invalid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123'
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'name',
                        'email',
                        'password'
                    ]
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Error de validación'
                ]);
    }
} 