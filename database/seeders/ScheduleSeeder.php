<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Support\Carbon;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maya = Employee::factory()->create([
            'first_name' => 'Maya',
            'last_name' => 'Reyes',
            'hire_date' => '2025-02-10',
        ]);

        $others = Employee::factory(3)->create();

        $monday = Carbon::parse('2026-09-28');

        foreach (range(0, 4) as $day) {
            $date = $monday->copy()->addDays($day);

            $opening = Shift::create(['shift_date' => $date, 'start_time' => '06:00', 'end_time' => '14:00']);
            $closing = Shift::create(['shift_date' => $date, 'start_time' => '12:00', 'end_time' => '20:00']);

            $opening->employees()->attach([$maya->id, $others[0]->id]);
            $closing->employees()->attach([$others[1]->id, $others[2]->id]);
        }
    }
}
