<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/**
 * @covers \App\Http\Controllers\Timetables\StoreTimetableController
 *
 * Pruebas de integración para la creación de horarios
 * Verifica la validación de datos y la seguridad
 */
class StoreTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Verifica que usuarios no autenticados no puedan crear horarios
     * Comprueba el código de respuesta 401
     */
    public function test_cannot_create_timetable_when_not_authenticated(): void
    {
        $timetableData = [
            'name' => 'Test Timetable',
            'description' => 'This is a test timetable description'
        ];

        $response = $this->postJson('/api/timetables', $timetableData);

        $response->assertStatus(401);
    }

    /**
     * Prueba la creación exitosa de un horario
     * Verifica la estructura de la respuesta y la persistencia en base de datos
     */
    public function test_can_create_timetable(): void
    {
        Sanctum::actingAs($this->user);

        $timetableData = [
            'name' => 'Test Timetable',
            'description' => 'This is a test timetable description'
        ];

        $response = $this->postJson('/api/timetables', $timetableData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'description',
                         'user_id',
                         'created_at',
                         'updated_at'
                     ]
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Horario creado exitosamente',
                     'data' => [
                         'name' => $timetableData['name'],
                         'description' => $timetableData['description'],
                         'user_id' => $this->user->id
                     ]
                 ]);

        $this->assertDatabaseHas('timetables', [
            'name' => $timetableData['name'],
            'description' => $timetableData['description'],
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Verifica el manejo de datos inválidos
     * Prueba las restricciones de longitud en nombre y descripción
     */
    public function test_cannot_create_timetable_with_invalid_data(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/timetables', [
            'name' => str_repeat('a', 51), // Más de 50 caracteres
            'description' => str_repeat('a', 301) // Más de 300 caracteres
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'errors' => [
                         'name',
                         'description'
                     ]
                 ])
                 ->assertJson([
                     'success' => false,
                     'message' => 'Error de validación'
                 ]);
    }

    /**
     * Prueba que no se pueda crear un horario sin los campos requeridos
     * Verifica que se devuelvan los errores de validación apropiados
     */
    public function test_cannot_create_timetable_without_required_fields(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/timetables', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'errors' => [
                         'name',
                         'description'
                     ]
                 ])
                 ->assertJson([
                     'success' => false,
                     'message' => 'Error de validación'
                 ]);
    }
} 