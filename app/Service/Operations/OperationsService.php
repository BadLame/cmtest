<?php

namespace App\Service\Operations;

use App\Exceptions\TransactionException;
use App\Models\UserBalance;

interface OperationsService
{
    /**
     * Внести сумму на счёт (создав при отсутствии)
     * @throws TransactionException
     */
    function deposit(UserBalance $ub, float $amount, ?string $comment = null): UserBalance;
}
