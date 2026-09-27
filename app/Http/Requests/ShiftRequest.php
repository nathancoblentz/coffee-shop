<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class ShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shift_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $minutes = Shift::minutesBetween($this->input('start_time'), $this->input('end_time'));

                if ($minutes > Shift::MAX_MINUTES) {
                    $validator->errors()->add('end_time', 'A shift can be at most 12 hours.');

                    return;
                }

                // Creating a shift: nobody's on it yet, so no one can go over 40.
                $shift = $this->route('shift');
                if (! $shift) {
                    return;
                }

                // Editing: check everyone already on it against the NEW date and times.
                $date = Carbon::parse($this->input('shift_date'));

                foreach ($shift->employees as $employee) {
                    $total = $employee->minutesInWeek($date, $shift->id) + $minutes;

                    if ($total > Employee::MAX_WEEKLY_MINUTES) {
                        $hours = round($total / 60, 1);
                        $validator->errors()->add(
                            'end_time',
                            "{$employee->first_name} would be at {$hours} hours that week. Max is 40."
                        );
                    }
                }
            },
        ];
    }
}
