<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShiftRequest;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Manages scheduling operations for the coffee shop.
 * A shift represents a work block that must stay within staffing and duration limits,
 * and the controller exposes the assignment flow used to place employees onto each shift.
 */
class ShiftController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Shifts/Index', [
            'shifts' => Shift::with('employees')
                ->orderBy('shift_date')
                ->orderBy('start_time')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Shifts/Create');
    }

    public function store(ShiftRequest $request): RedirectResponse
    {
        $shift = Shift::create($request->validated());

        // Go straight to the assignment page. That's the next thing you'd do anyway.
        return to_route('shifts.show', $shift)->with('success', 'Shift created. Now add people.');
    }

    public function show(Shift $shift): Response
    {
        $shift->load('employees');

        return Inertia::render('Shifts/Show', [
            'shift' => $shift,
            'maxEmployees' => Shift::MAX_EMPLOYEES,
            'maxWeeklyMinutes' => Employee::MAX_WEEKLY_MINUTES,
            'employees' => Employee::orderBy('first_name')->get()->map(fn (Employee $e) => [
                'id' => $e->id,
                'name' => "{$e->first_name} {$e->last_name}",
                'week_minutes' => $e->minutesInWeek($shift->shift_date),
                'on_shift' => $shift->employees->contains($e),
            ]),
        ]);
    }

    public function edit(Shift $shift): Response
    {
        return Inertia::render('Shifts/Edit', ['shift' => $shift]);
    }

    public function update(ShiftRequest $request, Shift $shift): RedirectResponse
    {
        $shift->update($request->validated());

        return to_route('shifts.show', $shift)->with('success', 'Shift updated.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $shift->delete(); // assignments cascade

        return to_route('shifts.index')->with('success', 'Shift deleted.');
    }
}
