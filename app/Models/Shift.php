<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['shift_date', 'start_time', 'end_time'];

    protected function casts(): array
    {
        return ['shift_date' => 'date'];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }

    protected function durationMinutes(): Attribute
    {
        return Attribute::get(fn () => (int) Carbon::parse($this->start_time)
            ->diffInMinutes(Carbon::parse($this->end_time)));
    }
}