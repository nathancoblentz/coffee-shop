<?php

namespace App\Http\Requests;

use App\Models\Ingredient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Untracked items (water, ice) never carry a quantity. Normalize before validating.
    protected function prepareForValidation(): void
    {
        // If the ingredient is not tracked in inventory, set quantity_on_hand to null.
        if (! $this->boolean('track_in_inventory')) {
            $this->merge(['quantity_on_hand' => null]);
        }
    }

    // Validation rules for creating or updating an ingredient.
    public function rules(): array
    {   // The name must be unique, the unit must be one of the defined units, and if the ingredient is tracked in inventory, quantity_on_hand must be a non-negative decimal.
        return [ //
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('ingredients', 'name')->ignore($this->route('ingredient')),
            ],
            'unit' => ['required', Rule::in(Ingredient::UNITS)],
            'track_in_inventory' => ['required', 'boolean'],
            'quantity_on_hand' => [
                'nullable', 'required_if_accepted:track_in_inventory',
                'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2',
            ],
        ];
    }
}
