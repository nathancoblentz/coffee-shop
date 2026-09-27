<?php

namespace App\Rules;

use App\Models\Shift;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxEmployeesPerShift implements ValidationRule
{
    public function __construct(private Shift $shift) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->shift->employees()->count() >= Shift::MAX_EMPLOYEES) {
            $fail('This shift already has '.Shift::MAX_EMPLOYEES.' people on it.');
        }
    }
}
