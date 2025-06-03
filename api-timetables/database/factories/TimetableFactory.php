<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetableFactory extends Factory
{
    protected $model = Timetable::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->text(50),
            'description' => $this->faker->text(300),
            'user_id' => User::factory()
        ];
    }

    public function forUser(User $user): self
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id
            ];
        });
    }
} 