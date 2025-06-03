<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/**
 * @covers \App\Http\Controllers\Timetables\ShowTimetableController
 */
class ShowTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_cannot_show_timetable_when_not_authenticated(): void
    {
        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        $response = $this->getJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(401);
    }

    public function test_can_show_timetable(): void
    {
        Sanctum::actingAs($this->user);

        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        $response = $this->getJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(200)
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
                    'message' => 'Horario encontrado',
                    'data' => [
                        'id' => $timetable->id,
                        'name' => $timetable->name,
                        'description' => $timetable->description,
                        'user_id' => $this->user->id
                    ]
                ]);
    }

    public function test_cannot_show_nonexistent_timetable(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/timetables/999');

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Horario no encontrado',
                    'errors' => ['No se encontró el recurso solicitado']
                ]);

        $this->assertTrue(true, 'La prueba verifica el manejo correcto de horarios inexistentes');
    }

    public function test_cannot_show_timetable_of_another_user(): void
    {
        $otherUser = User::factory()->create();
        $timetable = Timetable::factory()->for($otherUser)->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Horario no encontrado',
                    'errors' => ['No se encontró el recurso solicitado']
                ]);

        $this->assertTrue(true, 'La prueba verifica que un usuario no puede ver horarios de otros usuarios');
    }
} 