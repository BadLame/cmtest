<?php

namespace App\Repository\Transaction;

use App\Models\Transaction;

class SimpleTransactionRepository implements TransactionRepository
{
    function save(Transaction $transaction): Transaction
    {
        $transaction->save();
        return $transaction;
    }
}
