<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngredientRequest;
use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Maintains the inventory and recipe ingredients used by the coffee shop menu.
 * Ingredients define the recognized units and stock rules, and they cannot be removed while they are still
 * referenced by a menu item recipe.
 */
class IngredientController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Ingredients/Index', [
            'ingredients' => Ingredient::orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Ingredients/Create', ['units' => Ingredient::UNITS]);
    }

    public function store(IngredientRequest $request): RedirectResponse
    {
        Ingredient::create($request->validated());

        return to_route('ingredients.index')->with('success', 'Ingredient added.');
    }

    public function edit(Ingredient $ingredient): Response
    {
        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient,
            'units' => Ingredient::UNITS,
        ]);
    }

    public function update(IngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $ingredient->update($request->validated());

        return to_route('ingredients.index')->with('success', 'Ingredient updated.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        // The FK from ingredient_menu_item has no cascade, on purpose.
        // Say so nicely instead of letting the database throw.
        if ($ingredient->menuItems()->exists()) {
            return back()->withErrors([
                'ingredient' => "{$ingredient->name} is in a recipe. Take it out of those menu items first.",
            ]);
        }

        $ingredient->delete();

        return to_route('ingredients.index')->with('success', 'Ingredient deleted.');
    }
}
