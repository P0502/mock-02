<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;

class BreaksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $breakStart = $this->faker->dateTimeBetween('12:00:00', '13:00:00');

        $breakEnd = (clone $breakStart)->modify('+' . rand(45, 60) . 'minutes');

        return [
            'attendance_id' => Attendance::factory(),
            'start_time' => $breakStart->format('H:i:s'),
            'end_time' => $breakEnd->format('H:i:s'),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
