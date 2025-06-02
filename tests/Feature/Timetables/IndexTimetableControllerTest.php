<?php

namespace Tests\Feature\Timetables;

use Tests\TestCase;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTimetableControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_timetables(): void
    {
        $timetables = Timetable::factory()->count(3)->create();

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
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => $timetables->toArray()
                 ]);
    }

    public function test_empty_timetables_list(): void
    {
        $response = $this->getJson('/api/timetables');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => []
                 ]);
    }
} 