<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Manages employee records for staffing and schedule planning.
 * Employees are the people assigned to shifts, and their weekly-hour limits are enforced through
 * the scheduling logic associated with the Shift and Employee models.
 */
class EmployeeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Employees/Index', [
            'employees' => Employee::orderBy('last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Employees/Create');
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated());

        return to_route('employees.index')->with('success', 'Employee added.');
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Employees/Edit', ['employee' => $employee]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return to_route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return to_route('employees.index')->with('success', 'Employee deleted.');
    }
}
