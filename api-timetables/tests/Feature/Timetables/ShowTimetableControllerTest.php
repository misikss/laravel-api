<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_timetable(): void
    {
        $timetable = Timetable::factory()->create();

        $response = $this->getJson("/api/timetables/{$timetable->id}");

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
                     'data' => $timetable->toArray()
                 ]);
    }

    public function test_cannot_show_nonexistent_timetable(): void
    {
        $response = $this->getJson('/api/timetables/999');

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