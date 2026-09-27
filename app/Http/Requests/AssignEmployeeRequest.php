<?php

namespace App\Http\Requests;

use App\Rules\MaxEmployeesPerShift;
use App\Rules\MaxWeeklyHours;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shift = $this->route('shift');

        return [
            'employee_id' => [
                'bail',
                'required',
                'exists:employees,id',
                Rule::unique('employee_shift')->where('shift_id', $shift->id),
                new MaxEmployeesPerShift($shift),
                new MaxWeeklyHours($shift),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'That person is already on this shift.',
        ];
    }
}
