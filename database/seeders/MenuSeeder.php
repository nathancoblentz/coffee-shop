<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Support\Arr;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = collect([
            ['name' => 'Espresso beans',        'unit' => 'g',    'quantity_on_hand' => 5000],
            ['name' => 'Drip coffee grounds',   'unit' => 'g',    'quantity_on_hand' => 4000],
            ['name' => 'Cold brew concentrate', 'unit' => 'ml',   'quantity_on_hand' => 8000],
            ['name' => 'Whole milk',            'unit' => 'ml',   'quantity_on_hand' => 30000],
            ['name' => 'Oat milk',              'unit' => 'ml',   'quantity_on_hand' => 12000],
            ['name' => 'Vanilla syrup',         'unit' => 'ml',   'quantity_on_hand' => 1500],
            ['name' => 'Chocolate sauce',       'unit' => 'ml',   'quantity_on_hand' => 2000],
            ['name' => 'Hot cup 12 oz',         'unit' => 'each', 'quantity_on_hand' => 500],
            ['name' => 'Cold cup 16 oz',        'unit' => 'each', 'quantity_on_hand' => 300],
            ['name' => 'Lid',                   'unit' => 'each', 'quantity_on_hand' => 800],
            ['name' => 'Water',                 'unit' => 'ml',   'track_in_inventory' => false],
            ['name' => 'Ice',                   'unit' => 'g',    'track_in_inventory' => false],
        ])->mapWithKeys(fn ($row) => [$row['name'] => Ingredient::create($row)]);

        $menu = [
            ['name' => 'Espresso', 'description' => 'Double shot.', 'price' => 3.00,
            'directions' => 'Pull a double shot, 25-30 seconds. Serve in a demitasse.',
            'recipe' => ['Espresso beans' => 18]],

            ['name' => 'Americano', 'description' => 'Double shot topped with hot water.', 'price' => 3.50,
            'directions' => 'Fill cup with hot water, pull double shot over the top, lid.',
            'recipe' => ['Espresso beans' => 18, 'Water' => 250, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Latte', 'description' => 'Double shot with steamed whole milk.', 'price' => 4.75,
            'directions' => 'Pull double shot into cup. Steam milk to 60-65 C with light foam, pour, lid.',
            'recipe' => ['Espresso beans' => 18, 'Whole milk' => 240, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Oat Milk Latte', 'description' => 'Double shot with steamed oat milk.', 'price' => 5.25,
            'directions' => 'Pull double shot into cup. Steam oat milk to 55-60 C, pour, lid.',
            'recipe' => ['Espresso beans' => 18, 'Oat milk' => 240, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Cappuccino', 'description' => 'Double shot, less milk, thick foam.', 'price' => 4.50,
            'directions' => 'Pull double shot. Steam milk with heavy foam, pour to fill, lid.',
            'recipe' => ['Espresso beans' => 18, 'Whole milk' => 150, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Vanilla Latte', 'description' => 'Latte with vanilla syrup.', 'price' => 5.25,
            'directions' => 'Syrup in cup first. Pull double shot over it, add steamed milk, lid.',
            'recipe' => ['Espresso beans' => 18, 'Whole milk' => 220, 'Vanilla syrup' => 20, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Mocha', 'description' => 'Espresso, chocolate, steamed milk.', 'price' => 5.50,
            'directions' => 'Chocolate sauce in cup, pull double shot and stir, add steamed milk, lid.',
            'recipe' => ['Espresso beans' => 18, 'Whole milk' => 200, 'Chocolate sauce' => 30, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Drip Coffee', 'description' => 'House drip, 12 oz.', 'price' => 2.75,
            'directions' => 'Pour from the current batch. Brew ratio 22 g grounds to 350 ml water.',
            'recipe' => ['Drip coffee grounds' => 22, 'Water' => 350, 'Hot cup 12 oz' => 1, 'Lid' => 1]],

            ['name' => 'Cold Brew', 'description' => 'Cold brew concentrate over ice.', 'price' => 4.25,
            'directions' => 'Fill cup with ice, add concentrate, top with cold water, lid.',
            'recipe' => ['Cold brew concentrate' => 150, 'Water' => 150, 'Ice' => 150, 'Cold cup 16 oz' => 1, 'Lid' => 1]],
        ];

        foreach ($menu as $item) {
            $menuItem = MenuItem::create(Arr::except($item, 'recipe'));

            foreach ($item['recipe'] as $ingredientName => $quantity) {
                $menuItem->ingredients()->attach($ingredients[$ingredientName], ['quantity_used' => $quantity]);
            }
        }
    }
}
