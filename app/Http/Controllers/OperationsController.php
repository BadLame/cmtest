<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserBalanceResource;
use App\Models\UserBalance;
use RuntimeException;

class OperationsController extends Controller
{
    function balance(UserBalance $ub)
    {
        return new UserBalanceResource($ub);
    }

    function deposit()
    {
        throw new RuntimeException('To be implemented');
    }

    function withdraw()
    {
        throw new RuntimeException('To be implemented');
    }

    function transfer()
    {
        throw new RuntimeException('To be implemented');

    }
}
