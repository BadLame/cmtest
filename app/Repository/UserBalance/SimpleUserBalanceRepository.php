<?php

namespace App\Repository\UserBalance;

use App\Models\UserBalance;

class SimpleUserBalanceRepository implements UserBalanceRepository
{
    function getByUserId(int $userId): UserBalance
    {
        return UserBalance::query()->where('user_id', $userId)->firstOrFail();
    }

    function getOrNewByUserId(int $userId): UserBalance
    {
        return UserBalance::query()->firstOrNew(['user_id' => $userId]);
    }

    function save(UserBalance $ub): UserBalance
    {
        $ub->save();
        return $ub;
    }
}
