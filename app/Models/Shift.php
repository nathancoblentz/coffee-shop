<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Business rule: a shift can only be staffed by a limited number of employees and must remain within
 * a 12-hour maximum. Duration is calculated from the stored start and end times.
 */
class Shift extends Model
{
    use HasFactory;

    public const MAX_EMPLOYEES = 3;

    public const MAX_MINUTES = 12 * 60;

    protected $fillable = ['shift_date', 'start_time', 'end_time'];

    protected $appends = ['duration_minutes'];

    protected function casts(): array
    {
        return ['shift_date' => 'date:Y-m-d'];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }

    /** Pure math. No database. Used for saved shifts and for times someone just typed. */
    public static function minutesBetween(string $start, string $end): int
    {
        return (int) Carbon::parse($start)->diffInMinutes(Carbon::parse($end), true);
    }

    protected function durationMinutes(): Attribute
    {
        return Attribute::make(
            get: fn () => self::minutesBetween($this->start_time, $this->end_time),
        );
    }

    // Always hand times out as HH:MM so edit forms pass date_format:H:i.
    protected function startTime(): Attribute
    {
        return Attribute::make(get: fn ($value) => substr($value, 0, 5));
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(get: fn ($value) => substr($value, 0, 5));
    }
}
