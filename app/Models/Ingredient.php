<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'track_in_inventory', 'quantity_on_hand'];

    protected function casts(): array
    {
        return [
            'track_in_inventory' => 'boolean',
            'quantity_on_hand' => 'decimal:2',
        ];
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class)->withPivot('quantity_used');
    }
}