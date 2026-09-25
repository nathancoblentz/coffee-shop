<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'hire_date'];

    protected function casts(): array
    {
        return ['hire_date' => 'date'];
    }

    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class);
    }
}