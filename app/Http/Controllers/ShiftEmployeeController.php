<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignEmployeeRequest;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;

class ShiftEmployeeController extends Controller
{
    public function store(AssignEmployeeRequest $request, Shift $shift): RedirectResponse
    {
        $shift->employees()->attach($request->validated('employee_id'));

        return back()->with('success', 'Added to shift.');
    }

    public function destroy(Shift $shift, Employee $employee): RedirectResponse
    {
        $shift->employees()->detach($employee);

        return back()->with('success', 'Removed from shift.');
    }
}
