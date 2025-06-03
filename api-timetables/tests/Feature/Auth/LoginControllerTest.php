<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas de integración para el proceso de inicio de sesión
 * Verifica la autenticación y manejo de errores
 */
class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica el proceso exitoso de inicio de sesión
     * Comprueba la estructura de la respuesta y la generación del token
     */
    public function test_user_can_login(): void
    {
        $user = User::create([
            'name' => 'Test User',
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
    }

    /**
     * Prueba el manejo de credenciales inválidas
     * Verifica que se devuelva el mensaje de error apropiado
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::create([
            'name' => 'Test User',
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
                     'errors' => ['email' => ['Las credenciales proporcionadas son incorrectas']]
                 ]);
    }

    /**
     * Verifica las validaciones de formato en los campos de inicio de sesión
     * Comprueba que se manejen correctamente los errores de validación
     */
    public function test_login_validation_error(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            'password' => ''
        ]);

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