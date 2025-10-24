<?php

namespace Database\Factories;

use App\Models\UserBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserBalance> */
class UserBalanceFactory extends Factory
{
    // Иногда случались коллизии при создании более чем одной записи
    static public int $uniqueIdAdder = 1;

    function definition(): array
    {
        return [
            'user_id' => (UserBalance::query()->max('user_id') ?? 0)
                + (static::$uniqueIdAdder++),
            'balance' => fake()->randomFloat(2, 1, 100_000),
        ];
    }
}
