<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_authenticated_user(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'email',
                     'created_at',
                     'updated_at'
                 ]);
    }

    public function test_unauthenticated_user_cannot_get_user_info(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }
} 