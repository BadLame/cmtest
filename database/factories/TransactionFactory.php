<?php

namespace Database\Factories;

use App\Models\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/** @extends Factory<Transaction> */
class TransactionFactory extends Factory
{
    function definition(): array
    {
        $amount = fake()->randomFloat(2, 1, 100_500);

        return [
            'user_id' => UserBalance::factory([
                'balance' => fake()->randomFloat(2, $amount, $amount + 100),
            ]),
            'amount' => $amount,
            'type' => Arr::random(TransactionType::cases()),
        ];
    }
}
