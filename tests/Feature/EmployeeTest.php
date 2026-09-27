<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_creates_an_employee(): void
    {
        $this->post('/employees', [
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'hire_date' => '2026-01-05',
        ])->assertRedirect('/employees')->assertSessionHasNoErrors();

        $this->assertDatabaseHas('employees', ['first_name' => 'Ana', 'last_name' => 'Lopez']);
    }

    public function test_rejects_short_names_and_future_hire_dates(): void
    {
        $this->post('/employees', [
            'first_name' => 'A',
            'last_name' => '',
            'hire_date' => now()->addDay()->format('Y-m-d'),
        ])->assertSessionHasErrors(['first_name', 'last_name', 'hire_date']);

        $this->assertDatabaseCount('employees', 0);
    }

    public function test_updates_an_employee(): void
    {
        $employee = Employee::factory()->create();

        $this->put("/employees/{$employee->id}", [
            'first_name' => 'Renamed',
            'last_name' => $employee->last_name,
            'hire_date' => $employee->hire_date->format('Y-m-d'),
        ])->assertRedirect('/employees')->assertSessionHasNoErrors();

        $this->assertSame('Renamed', $employee->fresh()->first_name);
    }
}
