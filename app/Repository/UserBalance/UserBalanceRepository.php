<?php

namespace App\Repository\UserBalance;

use App\Models\UserBalance;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface UserBalanceRepository
{
    /**
     * Получить существующую запись или бросить исключение "запись не найдена"
     * @throws NotFoundHttpException
     */
    function getByUserId(int $userId, bool $withTransactions = true): UserBalance;

    /** Получить существующую или создать новую запись (без сохранения в БД) */
    function getOrNewByUserId(int $userId): UserBalance;

    /** Сохранить изменения записи в БД (создать при отсутствии) */
    function save(UserBalance $ub): UserBalance;
}
