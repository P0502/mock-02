<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Breaks;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
        
        for ($i = 0; $i < 30; $i++) {

                $date = Carbon::now()->subDays($i);

                $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => $date->format('Y-m-d')
            ]);

                Breaks::factory()->create([
                'attendance_id' => $attendance->id
                ]);
            } 
        });
    }
}
