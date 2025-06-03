<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/**
 * @covers \App\Http\Controllers\Timetables\IndexTimetableController
 */
class IndexTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_cannot_list_timetables_when_not_authenticated(): void
    {
        $response = $this->getJson('/api/timetables');

        $response->assertStatus(401);
    }

    public function test_can_list_timetables(): void
    {
        Sanctum::actingAs($this->user);

        $timetables = Timetable::factory()
            ->count(3)
            ->for($this->user)
            ->create();

        $response = $this->getJson('/api/timetables');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'description',
                             'user_id',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Lista de horarios'
                 ]);

        foreach ($timetables as $timetable) {
            $response->assertJsonFragment([
                'id' => $timetable->id,
                'name' => $timetable->name,
                'description' => $timetable->description,
                'user_id' => $this->user->id
            ]);
        }
    }

    public function test_empty_timetables_list(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/timetables');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Lista de horarios',
                     'data' => []
                 ]);
    }
} 