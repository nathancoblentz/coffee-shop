<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Business rule: every menu item has a price and a recipe built from ingredients.
 * The quantity_used pivot tells how much of each ingredient is required for the item.
 */
class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'directions'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('quantity_used');
    }
}
