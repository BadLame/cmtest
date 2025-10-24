<?php

namespace App\Providers\Repository;

use App\Repository\UserBalance\SimpleUserBalanceRepository;
use App\Repository\UserBalance\UserBalanceRepository;
use Illuminate\Support\ServiceProvider;

class UserBalanceRepositoryProvider extends ServiceProvider
{
    function register(): void
    {
        $this->app->bind(
            UserBalanceRepository::class,
            fn () => new SimpleUserBalanceRepository
        );
    }
}
