<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('menu_items', 'name')->ignore($this->route('menu_item')),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999.99', 'decimal:0,2'],
            'directions' => ['required', 'string'],

            'ingredients' => ['sometimes', 'array'],
            'ingredients.*.id' => ['required', 'distinct', 'exists:ingredients,id'],
            'ingredients.*.quantity_used' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
        ];
    }

    public function messages(): array
    {
        return [
            'ingredients.*.id.distinct' => 'Each ingredient can only appear once in a recipe.',
        ];
    }

    /** Recipe lines in the shape sync() wants: [ingredient_id => ['quantity_used' => x]]. */
    public function recipeLines(): array
    {
        return collect($this->validated('ingredients', []))
            ->mapWithKeys(fn ($line) => [$line['id'] => ['quantity_used' => $line['quantity_used']]])
            ->all();
    }
}
