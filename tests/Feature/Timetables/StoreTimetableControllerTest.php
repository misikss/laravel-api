<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_timetable(): void
    {
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
                         'created_at',
                         'updated_at'
                     ]
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Horario creado exitosamente',
                     'data' => [
                         'name' => $timetableData['name'],
                         'description' => $timetableData['description']
                     ]
                 ]);

        $this->assertDatabaseHas('timetables', $timetableData);
    }

    public function test_cannot_create_timetable_with_invalid_data(): void
    {
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

    public function test_cannot_create_timetable_without_required_fields(): void
    {
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