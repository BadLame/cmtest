<?php

namespace App\Providers\Repository;

use App\Repository\Transaction\SimpleTransactionRepository;
use App\Repository\Transaction\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class TransactionRepositoryProvider extends ServiceProvider
{
    function register(): void
    {
        $this->app->bind(
            TransactionRepository::class,
            fn () => new SimpleTransactionRepository
        );
    }
}
