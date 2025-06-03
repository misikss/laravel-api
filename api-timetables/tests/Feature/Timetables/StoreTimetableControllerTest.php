<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class StoreTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_cannot_create_timetable_when_not_authenticated(): void
    {
        $timetableData = [
            'name' => 'Test Timetable',
            'description' => 'This is a test timetable description'
        ];

        $response = $this->postJson('/api/timetables', $timetableData);

        $response->assertStatus(401);
    }

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