<?php

namespace Database\Factories;

use App\Models\UserBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserBalance> */
class UserBalanceFactory extends Factory
{
    function definition(): array
    {
        return [
            'user_id' => (UserBalance::query()->max('user_id') ?? 0) + 1,
            'balance' => fake()->randomFloat(2, 1, 100_000),
        ];
    }
}
