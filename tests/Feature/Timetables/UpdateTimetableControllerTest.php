<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_timetable(): void
    {
        $timetable = Timetable::factory()->create();
        
        $updateData = [
            'name' => 'Updated Timetable Name',
            'description' => 'Updated timetable description'
        ];

        $response = $this->putJson("/api/timetables/{$timetable->id}", $updateData);

        $response->assertStatus(200)
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
                     'message' => 'Horario actualizado exitosamente',
                     'data' => array_merge(['id' => $timetable->id], $updateData)
                 ]);

        $this->assertDatabaseHas('timetables', $updateData);
    }

    public function test_cannot_update_timetable_with_invalid_data(): void
    {
        $timetable = Timetable::factory()->create();

        $response = $this->putJson("/api/timetables/{$timetable->id}", [
            'name' => str_repeat('a', 51),
            'description' => str_repeat('a', 301)
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

    public function test_cannot_update_nonexistent_timetable(): void
    {
        $response = $this->putJson('/api/timetables/999', [
            'name' => 'Test Name',
            'description' => 'Test Description'
        ]);

        $response->assertStatus(404)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'errors'
                 ])
                 ->assertJson([
                     'success' => false,
                     'message' => 'Horario no encontrado'
                 ]);
    }
} 