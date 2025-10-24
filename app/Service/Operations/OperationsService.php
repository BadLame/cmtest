<?php

namespace App\Service\Operations;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransactionException;
use App\Models\UserBalance;

interface OperationsService
{
    /**
     * Внести сумму на счёт (создав при отсутствии)
     * @throws TransactionException
     */
    function deposit(UserBalance $ub, float $amount, ?string $comment = null): UserBalance;

    /**
     * Снять сумму со счёта
     * @throws TransactionException
     * @throws InsufficientFundsException
     */
    function withdraw(UserBalance $ub, float $amount, ?string $comment = null): UserBalance;

    /**
     * Перевести сумму на другой счёт
     * @throws TransactionException
     * @throws InsufficientFundsException
     */
    function transfer(UserBalance $ubOut, UserBalance $ubIn, float $amount, ?string $comment = null): UserBalance;
}
