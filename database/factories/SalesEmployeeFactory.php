<?php

namespace Database\Factories;

use App\Models\SalesEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesEmployee>
 */
class SalesEmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_code' => 'EMP' . fake()->unique()->numberBetween(100, 999),
            'name' => fake()->name(),
        ];
    }
}
