<?php

namespace Tests\Http\Controllers;

use App\Models\UserBalance;
use Tests\TestCase;

class OperationsControllerTest extends TestCase
{
    function testBalanceShowsBalanceForExistingRecord()
    {
        $ub = UserBalance::factory()->create();

        $this->getJson(route('operations.balance', $ub->user_id))
            ->assertSuccessful()
            ->assertJson(['data' => ['user_id' => $ub->user_id, 'balance' => $ub->balance]]);
    }

    function testBalanceReturns_404ForNonExistingRecord()
    {
        $this->getJson(route('operations.balance', 100_500))
            ->assertNotFound()
            ->assertJson(['data' => [], 'error' => 'Not found']);
    }
}
