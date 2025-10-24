<?php

namespace Tests\Http\Controllers;

use App\Models\UserBalance;
use App\Repository\UserBalance\SimpleUserBalanceRepository;
use App\Repository\UserBalance\UserBalanceRepository;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class OperationsControllerTest extends TestCase
{
    // balance

    function testBalanceShowsBalanceForExistingRecord()
    {
        $ub = UserBalance::factory()->create();

        $this->getJson(route('operations.balance', $ub->user_id))
            ->assertSuccessful()
            ->assertJson(['data' => ['user_id' => $ub->user_id, 'balance' => $ub->balance]]);
    }

    function testBalanceReturns_404ForNonExistingRecord()
    {
        $response = $this->getJson(route('operations.balance', 100_500))
            ->assertNotFound()
            ->assertJsonStructure(['message', 'errors']);

        $this->assertNotEmpty($response->json('message'));
        $this->assertNotEmpty($response->json('errors'));
    }

    // deposit

    function testDepositWithInvalidRequestDataReturnsErrors()
    {
        $validRequestData = [
            'user_id' => rand(1, 100_500),
            'amount' => fake()->randomFloat(2, 1, 100_500),
            'comment' => fake()->words(asText: true),
        ];
        $invalidCases = [
            ['user_id' => 1.33],
            ['user_id' => -1],
            ['amount' => 0.01], // Минимальная сумма - 1
        ];

        foreach ($invalidCases as $invalidCase) {
            $requestData = array_merge($validRequestData, $invalidCase);
            $this->postJson(route('operations.deposit'), $requestData)
                ->assertStatus(422);
        }
    }

    function testFailedTransactionDoesntAddAmountToBalance()
    {
        $ub = UserBalance::factory()->create();
        $amount = fake()->randomFloat(2, 1, 100_500);

        $this->instance(
            UserBalanceRepository::class,
            Mockery::mock(SimpleUserBalanceRepository::class)
                ->makePartial()
                ->shouldReceive('save')
                ->andReturnUsing(fn ($arg) => throw new RuntimeException)
                ->getMock()
        );

        $response = $this->postJson(route('operations.deposit'), [
            'user_id' => $ub->user_id,
            'amount' => $amount,
            'comment' => null,
        ])
            ->assertStatus(419)
            ->assertJsonStructure(['message', 'errors']);

        $this->assertEquals($ub->balance, $ub->fresh()->balance);
        $this->assertNotEmpty($response->json('message'));
        $this->assertNotEmpty($response->json('errors'));
    }

    function testDepositCreatesMissingUserBalanceRecord()
    {
        $requestData = [
            'user_id' => (UserBalance::query()->max('user_id') ?? 0) + 1,
            'amount' => fake()->randomFloat(2, 1, 100_500),
            'comment' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];

        $this->postJson(route('operations.deposit'), $requestData)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user_id' => $requestData['user_id'],
                    'balance' => $requestData['amount'],
                ],
            ]);

        $this->assertDatabaseHas('users_balances', [
            'user_id' => $requestData['user_id'],
            'balance' => $requestData['amount'],
        ]);
    }

    function testDepositAddsBalanceRight()
    {
        $amount = fake()->randomFloat(2, 1, 100_500);
        $ub = UserBalance::factory()->create();

        $this->postJson(route('operations.deposit'), [
            'user_id' => $ub->user_id,
            'amount' => $amount,
            'comment' => null,
        ])
            ->assertSuccessful()
            ->assertJsonFragment(['balance' => round($ub->balance + $amount, 2)]);

        $this->assertDatabaseHas('users_balances', [
            'user_id' => $ub->user_id,
            'balance' => $ub->balance + $amount,
        ]);
    }
}
