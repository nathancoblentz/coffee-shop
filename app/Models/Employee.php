<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Business rule: an employee may not exceed 40 hours of scheduled work in a Monday-Sunday week.
 * The weekly limit is expressed here as MAX_WEEKLY_MINUTES and checked via minutesInWeek().
 */
class Employee extends Model
{
    use HasFactory;

    public const MAX_WEEKLY_MINUTES = 40 * 60;

    protected $fillable = ['first_name', 'last_name', 'hire_date'];

    protected function casts(): array
    {
        return ['hire_date' => 'date:Y-m-d'];
    }

    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class);
    }

    /** Minutes worked in the Mon-Sun week containing $day, optionally ignoring one shift. */
    public function minutesInWeek(CarbonInterface $day, ?int $exceptShiftId = null): int
    {
        $monday = $day->copy()->startOfWeek(CarbonInterface::MONDAY);

        return $this->shifts()
            ->whereDate('shift_date', '>=', $monday)
            ->whereDate('shift_date', '<', $monday->copy()->addWeek())
            ->when($exceptShiftId, fn ($q) => $q->where('shifts.id', '!=', $exceptShiftId))
            ->get()
            ->sum('duration_minutes');
    }
}
