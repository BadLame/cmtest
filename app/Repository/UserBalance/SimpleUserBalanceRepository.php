<?php

namespace App\Repository\UserBalance;

use App\Models\UserBalance;
use Illuminate\Database\Eloquent\Builder;

class SimpleUserBalanceRepository implements UserBalanceRepository
{
    function getByUserId(int $userId, bool $withTransactions = true): UserBalance
    {
        return UserBalance::query()
            ->when($withTransactions, fn (Builder $q) => $q->with(['transactions' => fn ($q) => $q->orderByDesc('id')]))
            ->where('user_id', $userId)->firstOrFail();
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
