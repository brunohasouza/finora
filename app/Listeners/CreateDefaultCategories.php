<?php

namespace App\Listeners;

use App\Enums\CategoryTypes;
use App\Events\UserCreated;

class CreateDefaultCategories
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        $defaultCategories = [
            ['name' => 'Alimentação', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#E53A36'],
            ['name' => 'Transporte', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#D71A60'],
            ['name' => 'Lazer', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#8E24AA'],
            ['name' => 'Saúde', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#1D89E5'],
            ['name' => 'Moradia', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#00897A'],
            ['name' => 'Educação', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#00ACC2'],
            ['name' => 'Outros', 'type' => CategoryTypes::EXPENSE->value, 'color' => '#6D4C42'],
            ['name' => 'Salário', 'type' => CategoryTypes::INCOME->value, 'color' => '#5D35B0'],
            ['name' => 'Empréstimos', 'type' => CategoryTypes::INCOME->value, 'color' => '#3948AB'],
            ['name' => 'Investimentos', 'type' => CategoryTypes::INCOME->value, 'color' => '#00C852'],
            ['name' => 'Presente', 'type' => CategoryTypes::INCOME->value, 'color' => '#FFB300'],
            ['name' => 'Outros', 'type' => CategoryTypes::INCOME->value, 'color' => '#546F79'],
        ];

        foreach ($defaultCategories as $category) {
            $user->categories()->create($category);
        }
    }
}
