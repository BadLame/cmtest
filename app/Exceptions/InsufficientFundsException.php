<?php

namespace App\Exceptions;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Str;
use Throwable;

class InsufficientFundsException extends Exception
{
    function __construct(Transaction $transaction, ?Throwable $previous = null)
    {
        $message = sprintf(
            'You don\'t have enough funds to %s %.2f',
            Str::lower($transaction->type->value),
            $transaction->amount
        );
        parent::__construct($message, previous: $previous);
    }
}
