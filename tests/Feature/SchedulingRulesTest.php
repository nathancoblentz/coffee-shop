<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulingRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed explicitly. $seed = true only seeds if this class is the first to migrate,
        // which isn't the case when the whole suite runs.
        $this->seed();

        $this->actingAs(User::factory()->create());
    }

    private function shiftOn(string $date, string $start): Shift
    {
        return Shift::whereDate('shift_date', $date)->get()->firstWhere('start_time', $start);
    }

    private function employeeNamed(string $first): Employee
    {
        return Employee::where('first_name', $first)->firstOrFail();
    }

    public function test_rejects_a_fourth_person_on_a_shift(): void
    {
        $shift = $this->shiftOn('2026-09-28', '06:00');

        $this->post("/shifts/{$shift->id}/employees", ['employee_id' => $this->employeeNamed('Jordan')->id])
            ->assertSessionHasErrors('employee_id');

        $this->assertSame(3, $shift->employees()->count());
    }

    public function test_rejects_an_assignment_that_pushes_someone_past_40_hours(): void
    {
        $shift = $this->shiftOn('2026-09-28', '12:00');

        $this->post("/shifts/{$shift->id}/employees", ['employee_id' => $this->employeeNamed('Maya')->id])
            ->assertSessionHasErrors('employee_id');
    }

    public function test_rejects_stretching_a_shift_past_40_hours_for_someone_on_it(): void
    {
        $shift = $this->shiftOn('2026-10-02', '06:00');

        $this->put("/shifts/{$shift->id}", [
            'shift_date' => '2026-10-02',
            'start_time' => '06:00',
            'end_time' => '15:00',
        ])->assertSessionHasErrors('end_time');
    }

    public function test_allows_a_valid_assignment(): void
    {
        $shift = $this->shiftOn('2026-09-28', '12:00');

        $this->post("/shifts/{$shift->id}/employees", ['employee_id' => $this->employeeNamed('Jordan')->id])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $shift->employees()->count());
    }
}
