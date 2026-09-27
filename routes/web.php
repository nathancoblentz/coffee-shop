<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftEmployeeController;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * Public storefront and authenticated management routes.
 * The menu is available to all visitors, while scheduling and inventory management remain behind
 * the verified-auth workflow so only staff with valid accounts can edit operational data.
 */
// Business rule: the coffee shop has a menu of items, each with a price and a recipe built from ingredients.

Route::get('/menu', function () {
    return Inertia::render('Menu', [
        'menuItems' => MenuItem::orderBy('id')->get(['id', 'name', 'description', 'price']),
    ]);
})->name('menu');

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('employees', EmployeeController::class)->except('show');
    Route::resource('ingredients', IngredientController::class)->except('show');
    Route::resource('menu-items', MenuItemController::class)->except('show');
    Route::resource('shifts', ShiftController::class);

    Route::post('shifts/{shift}/employees', [ShiftEmployeeController::class, 'store'])
        ->name('shifts.employees.store');

    Route::delete('shifts/{shift}/employees/{employee}', [ShiftEmployeeController::class, 'destroy'])
        ->name('shifts.employees.destroy');
});

require __DIR__.'/settings.php';
