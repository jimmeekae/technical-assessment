<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $randomLetter = $letters[rand(0, strlen($letters) - 1)];
        $suffixLetter = $letters[rand(0, strlen($letters) - 1)];

        return [
            'registration_number' => 'KD' . $randomLetter . ' ' . fake()->numberBetween(100, 999) . $suffixLetter,
        ];
    }
}
