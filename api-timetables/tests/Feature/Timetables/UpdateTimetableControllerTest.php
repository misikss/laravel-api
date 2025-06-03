<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class UpdateTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_cannot_update_timetable_when_not_authenticated(): void
    {
        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        $updateData = [
            'name' => 'Test Name',
            'description' => 'Test Description'
        ];

        $response = $this->putJson("/api/timetables/{$timetable->id}", $updateData);

        $response->assertStatus(401);
    }

    public function test_can_update_timetable(): void
    {
        Sanctum::actingAs($this->user);

        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        $updateData = [
            'name' => 'Test Name',
            'description' => 'Test Description'
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
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Horario actualizado exitosamente',
                    'data' => [
                        'id' => $timetable->id,
                        'name' => $updateData['name'],
                        'description' => $updateData['description'],
                        'user_id' => $this->user->id
                    ]
                ]);

        $this->assertDatabaseHas('timetables', [
            'id' => $timetable->id,
            'name' => $updateData['name'],
            'description' => $updateData['description']
        ]);
    }

    public function test_cannot_update_timetable_with_invalid_data(): void
    {
        Sanctum::actingAs($this->user);

        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

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
                ]);
    }

    public function test_cannot_update_nonexistent_timetable(): void
    {
        Sanctum::actingAs($this->user);

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
                    'message' => 'Horario no encontrado',
                    'errors' => ['No se encontró el recurso solicitado']
                ]);
    }

    public function test_cannot_update_timetable_of_other_user(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $timetable = Timetable::factory()
            ->for($otherUser)
            ->create();

        $response = $this->putJson("/api/timetables/{$timetable->id}", [
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
                    'message' => 'Horario no encontrado',
                    'errors' => ['No se encontró el recurso solicitado']
                ]);
    }
} 