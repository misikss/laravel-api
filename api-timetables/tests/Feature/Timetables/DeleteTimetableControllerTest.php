<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class DeleteTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_cannot_delete_timetable_when_not_authenticated(): void
    {
        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        $response = $this->deleteJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(401);
    }

    public function test_can_delete_timetable(): void
    {
        Sanctum::actingAs($this->user);

        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        $response = $this->deleteJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Horario eliminado exitosamente'
                 ]);

        $this->assertDatabaseMissing('timetables', [
            'id' => $timetable->id
        ]);
    }

    public function test_cannot_delete_nonexistent_timetable(): void
    {
        Sanctum::actingAs($this->user);

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