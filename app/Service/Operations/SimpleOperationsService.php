<?php

namespace App\Service\Operations;

use App\Exceptions\TransactionException;
use App\Models\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\UserBalance;
use App\Repository\Transaction\TransactionRepository;
use App\Repository\UserBalance\UserBalanceRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

class SimpleOperationsService implements OperationsService
{
    function __construct(
        protected TransactionRepository $transactionRepo,
        protected UserBalanceRepository $ubRepo
    )
    {
    }

    function deposit(UserBalance $ub, float $amount, ?string $comment = null): UserBalance
    {
        $transaction = new Transaction;
        $transaction->user_id = $ub->user_id;
        $transaction->type = TransactionType::DEPOSIT;
        $transaction->amount = $amount;
        $transaction->comment = $comment;

        $ub->balance += $transaction->amount;

        DB::beginTransaction();
        try {
            $this->ubRepo->save($ub);
            $this->transactionRepo->save($transaction);
            DB::commit();
        } catch (Throwable $previous) {
            DB::rollBack();
            throw new TransactionException($transaction, $previous);
        }

        return $ub;
    }
}
