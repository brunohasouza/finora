<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $banksJson = file_get_contents(storage_path('app/private/banks.json'));
        $banks = json_decode($banksJson, true);
        $randomBank = fake()->randomElement($banks);

        return [
            'code' => $randomBank['code'],
            'shortname' => $randomBank['shortname'],
            'name' => $randomBank['name'],
        ];
    }
}
