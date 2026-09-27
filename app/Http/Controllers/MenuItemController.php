<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuItemRequest;
use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Manages the coffee shop menu and the recipe data that connects each item to its ingredients.
 * Menu items are created and edited as a single transaction so the ingredients relationship stays consistent.
 */
class MenuItemController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('MenuItems/Index', [
            'menuItems' => MenuItem::orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('MenuItems/Create', [
            'ingredients' => $this->ingredientOptions(),
        ]);
    }

    public function store(MenuItemRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $menuItem = MenuItem::create($request->safe()->except('ingredients'));
            $menuItem->ingredients()->sync($request->recipeLines());
        });

        return to_route('menu-items.index')->with('success', 'Menu item added.');
    }

    public function edit(MenuItem $menuItem): Response
    {
        return Inertia::render('MenuItems/Edit', [
            'menuItem' => $menuItem->load('ingredients'),
            'ingredients' => $this->ingredientOptions(),
        ]);
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        DB::transaction(function () use ($request, $menuItem) {
            $menuItem->update($request->safe()->except('ingredients'));

            // Only touch the recipe if the form sent one. A missing key must not wipe it.
            if ($request->has('ingredients')) {
                $menuItem->ingredients()->sync($request->recipeLines());
            }
        });

        return to_route('menu-items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete(); // recipe lines cascade

        return to_route('menu-items.index')->with('success', 'Menu item deleted.');
    }

    private function ingredientOptions()
    {
        return Ingredient::orderBy('name')->get(['id', 'name', 'unit']);
    }
}
