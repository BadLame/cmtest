<?php

namespace App\Providers\Service;

use App\Repository\Transaction\TransactionRepository;
use App\Repository\UserBalance\UserBalanceRepository;
use App\Service\Operations\OperationsService;
use App\Service\Operations\SimpleOperationsService;
use Illuminate\Support\ServiceProvider;

class OperationsServiceProvider extends ServiceProvider
{
    function register(): void
    {
        $this->app->bind(
            OperationsService::class,
            fn () => new SimpleOperationsService(
                app(TransactionRepository::class),
                app(UserBalanceRepository::class)
            )
        );
    }
}
