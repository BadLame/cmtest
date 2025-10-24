<?php

namespace App\Exceptions;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Str;
use Throwable;

/** Исключение неудачи сохранения операции в БД */
class TransactionException extends Exception
{
    function __construct(Transaction $transaction, ?Throwable $previous = null)
    {
        $message = sprintf(
            'Error while %s with sum %.2f for user %s',
            Str::lower($transaction->type->value),
            $transaction->amount,
            $transaction->user_id
        );
        parent::__construct($message, previous: $previous);
    }
}
