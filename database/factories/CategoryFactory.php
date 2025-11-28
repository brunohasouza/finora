<?php

namespace Database\Factories;

use App\Enums\CategoryTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'type' => fake()->randomElement(CategoryTypes::class),
            'user_id' => fake()->randomElement([8, 9]),
        ];
    }
}
