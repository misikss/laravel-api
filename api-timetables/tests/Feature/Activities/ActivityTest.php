<?php

namespace Tests\Feature\Activities;

use App\Models\Activity;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Activities\IndexActivityController
 * @covers \App\Http\Controllers\Activities\StoreActivityController
 * @covers \App\Http\Controllers\Activities\ShowActivityController
 * @covers \App\Http\Controllers\Activities\UpdateActivityController
 * @covers \App\Http\Controllers\Activities\DeleteActivityController
 */
class ActivityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Timetable $timetable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->timetable = Timetable::factory()->for($this->user)->create();
    }

    /**
     * Verifica que un usuario puede listar solo sus propias actividades
     * y que las actividades de otros usuarios no son visibles
     */
    public function test_user_can_list_their_activities(): void
    {
        $activities = Activity::factory(3)->create([
            'user_id' => $this->user->id,
            'timetable_id' => $this->timetable->id
        ]);

        $otherUserActivities = Activity::factory(2)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/activities');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'weekday',
                            'start_time',
                            'duration',
                            'information',
                            'is_available',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);

        $response->assertJsonCount(3, 'data.data');

        foreach ($activities as $activity) {
            $response->assertJsonFragment([
                'id' => $activity->id,
                'user_id' => $this->user->id
            ]);
        }
    }

    /**
     * Verifica la creación de actividades con datos válidos
     * y la estructura correcta de la respuesta
     */
    public function test_user_can_create_activity(): void
    {
        $activityData = [
            'weekday' => 1,
            'start_time' => '09:00',
            'duration' => 60,
            'information' => 'Test Activity',
            'is_available' => true,
            'timetable_id' => $this->timetable->id
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/activities', $activityData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'weekday',
                    'start_time',
                    'duration',
                    'information',
                    'is_available',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Actividad creada exitosamente',
                'data' => $activityData
            ]);

        $this->assertDatabaseHas('activities', [
            'user_id' => $this->user->id,
            'weekday' => 1,
            'start_time' => '09:00',
            'duration' => 60,
            'information' => 'Test Activity',
            'is_available' => true,
            'timetable_id' => $this->timetable->id
        ]);
    }

    /**
     * Prueba las validaciones de datos al crear una actividad
     * Verifica el manejo de errores para cada campo requerido
     */
    public function test_user_cannot_create_activity_with_invalid_data(): void
    {
        $invalidData = [
            'weekday' => 8, // Inválido: debe ser entre 1 y 7
            'start_time' => '25:00', // Inválido: formato de hora incorrecto
            'duration' => 0, // Inválido: debe ser mayor a 0
            'information' => '',
            'is_available' => 'invalid' // Inválido: debe ser booleano
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/activities', $invalidData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'weekday',
                    'start_time',
                    'duration',
                    'information'
                ]
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ]);

        $this->assertDatabaseCount('activities', 0);
    }

    /**
     * Verifica que un usuario puede ver los detalles de sus actividades
     * y que la respuesta contiene todos los campos necesarios
     */
    public function test_user_can_view_their_activity(): void
    {
        $activity = Activity::factory()->create([
            'user_id' => $this->user->id,
            'timetable_id' => $this->timetable->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/activities/{$activity->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'weekday',
                    'start_time',
                    'duration',
                    'information',
                    'is_available',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Actividad encontrada',
                'data' => [
                    'id' => $activity->id,
                    'user_id' => $this->user->id
                ]
            ]);
    }

    /**
     * Prueba la seguridad: un usuario no puede ver actividades
     * que pertenecen a otros usuarios
     */
    public function test_user_cannot_view_others_activity(): void
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/activities/{$activity->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'No autorizado'
            ]);
    }

    /**
     * Verifica la actualización de actividades con datos válidos
     * y que los cambios se reflejan en la base de datos
     */
    public function test_user_can_update_their_activity(): void
    {
        $activity = Activity::factory()->create([
            'user_id' => $this->user->id,
            'timetable_id' => $this->timetable->id
        ]);

        $updateData = [
            'weekday' => 2,
            'start_time' => '10:30',
            'duration' => 45,
            'information' => 'Updated Activity',
            'is_available' => false
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/activities/{$activity->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'weekday',
                    'start_time',
                    'duration',
                    'information',
                    'is_available',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Actividad actualizada exitosamente',
                'data' => $updateData
            ]);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'user_id' => $this->user->id,
            'weekday' => 2,
            'start_time' => '10:30',
            'duration' => 45,
            'information' => 'Updated Activity',
            'is_available' => false,
            'timetable_id' => $this->timetable->id
        ]);
    }

    /**
     * Prueba la seguridad: un usuario no puede modificar actividades
     * que pertenecen a otros usuarios
     */
    public function test_user_cannot_update_others_activity(): void
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/activities/{$activity->id}", [
                'weekday' => 2
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'No autorizado'
            ]);
    }

    /**
     * Verifica que un usuario puede eliminar sus propias actividades
     * y que se eliminan correctamente de la base de datos
     */
    public function test_user_can_delete_their_activity(): void
    {
        $activity = Activity::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/activities/{$activity->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Actividad eliminada exitosamente'
            ]);

        $this->assertDatabaseMissing('activities', [
            'id' => $activity->id
        ]);
    }

    /**
     * Prueba la seguridad: un usuario no puede eliminar actividades
     * que pertenecen a otros usuarios
     */
    public function test_user_cannot_delete_others_activity(): void
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/activities/{$activity->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'No autorizado'
            ]);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id
        ]);
    }
} 