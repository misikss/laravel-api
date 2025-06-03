<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Activity;
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

    public function test_can_show_timetable_with_activities(): void
    {
        Sanctum::actingAs($this->user);

        $timetable = Timetable::factory()
            ->for($this->user)
            ->create();

        // Crear algunas actividades asociadas al horario
        Activity::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'timetable_id' => $timetable->id
        ]);

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
                    'updated_at',
                    'activities' => [
                        '*' => [
                            'id',
                            'user_id',
                            'timetable_id',
                            'weekday',
                            'start_time',
                            'duration',
                            'information',
                            'is_available'
                        ]
                    ]
                ]
            ]);

        $this->assertCount(3, $response->json('data.activities'));
    }

    public function test_cannot_show_timetable_of_other_user(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $timetable = Timetable::factory()
            ->for($otherUser)
            ->create();

        $response = $this->getJson("/api/timetables/{$timetable->id}");

        $response->assertStatus(404);
    }
} 