<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'timetable_id' => Timetable::factory(),
            'weekday' => fake()->numberBetween(1, 7),
            'start_time' => fake()->time('H:i'),
            'duration' => fake()->numberBetween(15, 180),
            'information' => fake()->sentence(),
            'is_available' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the activity belongs to a specific user.
     */
    public function forUser(User $user): self
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    /**
     * Indicate that the activity belongs to a specific timetable.
     */
    public function forTimetable(Timetable $timetable): self
    {
        return $this->state(function (array $attributes) use ($timetable) {
            return [
                'timetable_id' => $timetable->id,
            ];
        });
    }
} 