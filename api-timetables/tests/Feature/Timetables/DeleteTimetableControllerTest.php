<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_timetable(): void
    {
        $timetable = Timetable::factory()->create();

        $response = $this->deleteJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Horario eliminado exitosamente',
                     'data' => []
                 ]);

        $this->assertDatabaseMissing('timetables', [
            'id' => $timetable->id
        ]);
    }

    public function test_cannot_delete_nonexistent_timetable(): void
    {
        $response = $this->deleteJson('/api/timetables/999');

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