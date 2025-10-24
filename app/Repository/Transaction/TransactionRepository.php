<?php

namespace App\Repository\Transaction;

use App\Models\Transaction;

interface TransactionRepository
{
    /** Сохранить изменения записи в БД (создать при отсутствии) */
    function save(Transaction $transaction): Transaction;
}
