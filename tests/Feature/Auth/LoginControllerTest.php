<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
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
                    'message' => 'Inicio de sesión exitoso'
                ]);

        $this->assertNotNull($response->json('data.token'));
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Credenciales inválidas',
                    'errors' => ['Las credenciales proporcionadas son incorrectas']
                ]);
    }

    public function test_login_validation_error(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
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