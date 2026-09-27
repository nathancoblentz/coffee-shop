<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Named, not random: SchedulingRulesTest looks people up by first name.
        $maya = Employee::create(['first_name' => 'Maya', 'last_name' => 'Reyes', 'hire_date' => '2025-02-10']);
        $sam = Employee::create(['first_name' => 'Sam', 'last_name' => 'Okafor', 'hire_date' => '2024-06-03']);
        $priya = Employee::create(['first_name' => 'Priya', 'last_name' => 'Nair', 'hire_date' => '2025-09-15']);
        $jordan = Employee::create(['first_name' => 'Jordan', 'last_name' => 'Blake', 'hire_date' => '2026-01-12']);

        $monday = Carbon::parse('2026-09-28');

        // Week totals: Maya 40, Sam 40, Priya 40, Jordan 32.
        // Monday opening is full (3), Monday closing is empty, Jordan has 8 hours of room.
        foreach (range(0, 4) as $day) {
            $date = $monday->copy()->addDays($day);

            $opening = Shift::create(['shift_date' => $date, 'start_time' => '06:00', 'end_time' => '14:00']);
            $closing = Shift::create(['shift_date' => $date, 'start_time' => '12:00', 'end_time' => '20:00']);

            $opening->employees()->attach([$maya->id, $sam->id]);

            if ($day === 0) {
                $opening->employees()->attach($priya->id);
            } else {
                $closing->employees()->attach([$jordan->id, $priya->id]);
            }
        }
    }
}
