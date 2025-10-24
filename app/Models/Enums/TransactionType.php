<?php

namespace App\Models\Enums;

enum TransactionType: string
{
    /** Пополнение */
    case DEPOSIT = 'DEPOSIT';
    /** Снятие */
    case WITHDRAW = 'WITHDRAW';
    /** Пополнение от другого пользователя */
    case TRANSFER_IN = 'TRANSFER_IN';
    /** Перевод другому пользователю */
    case TRANSFER_OUT = 'TRANSFER_OUT';
}
