<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Maize Flour 2KG',
            'Maize Flour 5KG',
            'Wheat Flour 2KG',
            'Rice 5KG',
            'Sugar 1KG',
            'Animal Feed 50KG',
        ];
    
        return [
            'item_code' => 'ITEM' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->randomElement($products),
            'unit_price' => fake()->randomFloat(3, 50, 5000),
        ];
    }
}
