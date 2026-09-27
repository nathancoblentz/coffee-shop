<?php

namespace App\Rules;

use App\Models\Employee;
use App\Models\Shift;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxWeeklyHours implements ValidationRule
{
    public function __construct(private Shift $shift) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $employee = Employee::find($value);

        $total = $employee->minutesInWeek($this->shift->shift_date, $this->shift->id)
               + $this->shift->duration_minutes;

        if ($total > Employee::MAX_WEEKLY_MINUTES) {
            $hours = round($total / 60, 1);
            $fail("{$employee->first_name} would be at {$hours} hours this week. Max is 40.");
        }
    }
}
