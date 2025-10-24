<?php

namespace App\Service\Operations;

use App\Exceptions\InsufficientFundsException;
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

    function withdraw(UserBalance $ub, float $amount, ?string $comment = null): UserBalance
    {
        $transaction = new Transaction;
        $transaction->user_id = $ub->user_id;
        $transaction->type = TransactionType::WITHDRAW;
        $transaction->amount = $amount;
        $transaction->comment = $comment;

        if ($ub->balance < $transaction->amount) {
            throw new InsufficientFundsException($transaction);
        }

        $ub->balance -= $transaction->amount;

        DB::beginTransaction();
        try {
            $this->ubRepo->save($ub);
            $this->transactionRepo->save($transaction);
        } catch (Throwable $previous) {
            DB::rollBack();
            throw new TransactionException($transaction, $previous);
        }

        return $ub;
    }

    function transfer(UserBalance $ubOut, UserBalance $ubIn, float $amount, ?string $comment = null): UserBalance
    {
        $tOut = new Transaction;
        $tOut->user_id = $ubOut->user_id;
        $tOut->type = TransactionType::TRANSFER_OUT;
        $tOut->amount = $amount;
        $tOut->comment = $comment;

        if ($ubOut->balance < $tOut->amount) {
            throw new InsufficientFundsException($tOut);
        }

        $tIn = $tOut->replicate(['type', 'user_id']);
        $tIn->type = TransactionType::TRANSFER_IN;
        $tIn->user_id = $ubIn->user_id;

        $ubOut->balance -= $tOut->amount;
        $ubIn->balance += $tOut->amount;

        DB::beginTransaction();
        try {
            $this->ubRepo->save($ubOut);
            $this->ubRepo->save($ubIn);
            $this->transactionRepo->save($tOut);
            $this->transactionRepo->save($tIn);
            DB::commit();
        } catch (Throwable $previous) {
            DB::rollBack();
            throw new TransactionException($tOut, $previous);
        }

        return $ubOut;
    }
}
