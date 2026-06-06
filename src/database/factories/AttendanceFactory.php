<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('08:00', '10:00');

        $end = (clone $start)->modify('+9 hours');

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->date(),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
