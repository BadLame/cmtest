<?php

namespace Tests\Http\Controllers;

use App\Models\Enums\TransactionType;
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
            ->assertOk()
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
            ->assertStatus(409)
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
        $requestData = [
            'user_id' => $ub->user_id,
            'amount' => $amount,
            'comment' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];

        $this->postJson(route('operations.deposit'), $requestData)
            ->assertOk()
            ->assertJsonFragment(['balance' => round($ub->balance + $amount, 2)]);

        $this->assertDatabaseHas('users_balances', [
            'user_id' => $ub->user_id,
            'balance' => $ub->balance + $amount,
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $ub->user_id,
            'amount' => $amount,
            'type' => TransactionType::DEPOSIT->value,
            'comment' => $requestData['comment'],
        ]);
    }

    // withdraw

    function testWithdrawFromNotExistingBalanceReturnsNotFound()
    {
        $nonExistingUserId = (UserBalance::query()->max('user_id') ?: 0) + 1;

        $this->postJson(route('operations.withdraw'), [
            'user_id' => $nonExistingUserId,
            'amount' => fake()->randomFloat(2, 1, 100_500),
            'comment' => fake()->words(asText: true),
        ])
            ->assertNotFound();
    }

    function testWithdrawWithInsufficientFundsReturnsErrors()
    {
        $ub = UserBalance::factory()->create();
        $requestData = [
            'user_id' => $ub->user_id,
            'amount' => $ub->balance + 0.01,
            'comment' => null,
        ];

        $this->postJson(route('operations.withdraw'), $requestData)
            ->assertStatus(409);

        $this->assertEquals($ub->balance, $ub->fresh()->balance);
    }

    function testWithdrawSubtractsBalanceRight()
    {
        $ub = UserBalance::factory()->create();
        $amount = fake()->randomFloat(2, 1, $ub->balance);
        $requestData = [
            'user_id' => $ub->user_id,
            'amount' => $amount,
            'comment' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];

        $this->postJson(route('operations.withdraw'), $requestData)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'user_id' => $ub->user_id,
                    'balance' => round($ub->balance - $amount, 2),
                ],
            ]);

        $this->assertEquals(round($ub->balance - $amount, 2), $ub->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $ub->user_id,
            'amount' => $amount,
            'type' => TransactionType::WITHDRAW->value,
            'comment' => $requestData['comment'],
        ]);
    }

    // transfer

    function testTransferWithNonExistingBalancesReturnsNotFound()
    {
        [$ubOut, $ubIn] = UserBalance::factory(2)->create();
        $validRequestData = [
            'from_user_id' => $ubOut->user_id,
            'to_user_id' => $ubIn->user_id,
            'amount' => fake()->randomFloat(2, 1, 100_500),
            'comment' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];
        $nonExistingUserId = (UserBalance::query()->max('user_id') ?: 0) + 1;
        $invalidCases = [
            ['from_user_id' => $nonExistingUserId],
            ['to_user_id' => $nonExistingUserId],
        ];

        foreach ($invalidCases as $invalidCase) {
            $requestData = array_merge($validRequestData, $invalidCase);
            $this->postJson(route('operations.transfer'), $requestData)
                ->assertNotFound();
        }
    }

    function testTransferWithInsufficientFundsReturnsErrors()
    {
        [$ubOut, $ubIn] = UserBalance::factory(2)->create();
        $requestData = [
            'from_user_id' => $ubOut->user_id,
            'to_user_id' => $ubIn->user_id,
            'amount' => $ubOut->balance + 0.01,
            'comment' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];

        $this->postJson(route('operations.transfer'), $requestData)
            ->assertStatus(409);

        $this->assertEquals($ubOut->balance, $ubOut->fresh()->balance);
        $this->assertEquals($ubIn->balance, $ubIn->fresh()->balance);
    }

    function testTransferManagesBalancesRight()
    {
        $rnd2 = fn (float $n) => round($n, 2);
        [$ubOut, $ubIn] = UserBalance::factory(2)->create();
        $amount = fake()->randomFloat(2, 1, $ubOut->balance);
        $requestData = [
            'from_user_id' => $ubOut->user_id,
            'to_user_id' => $ubIn->user_id,
            'amount' => $amount,
            'comment' => fake()->boolean() ? fake()->words(asText: true) : null,
        ];

        $this->postJson(route('operations.transfer'), $requestData)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'user_id' => $ubOut->user_id,
                    'balance' => round($ubOut->balance - $amount, 2),
                ],
            ]);

        $this->assertEquals(
            $rnd2($ubOut->balance - $amount),
            $rnd2($ubOut->fresh()->balance)
        );
        $this->assertEquals(
            $rnd2($ubIn->balance + $amount),
            $rnd2($ubIn->fresh()->balance)
        );
        $this->assertDatabaseHas('transactions', [
            'user_id' => $ubOut->user_id,
            'amount' => $amount,
            'type' => TransactionType::TRANSFER_OUT->value,
            'comment' => $requestData['comment'],
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $ubIn->user_id,
            'amount' => $amount,
            'type' => TransactionType::TRANSFER_IN->value,
            'comment' => $requestData['comment'],
        ]);
    }
}
