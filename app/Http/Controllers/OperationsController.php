<?php

namespace App\Http\Controllers;

use App\Http\Requests\Operations\DepositRequest;
use App\Http\Resources\UserBalanceResource;
use App\Repository\UserBalance\UserBalanceRepository;
use App\Service\Operations\OperationsService;
use RuntimeException;

class OperationsController extends Controller
{
    function __construct(
        protected UserBalanceRepository $ubRepo,
        protected OperationsService     $operationsService,
    )
    {
    }

    function balance(int $user_id): UserBalanceResource
    {
        return new UserBalanceResource(
            $this->ubRepo->getByUserId($user_id)
        );
    }

    function deposit(DepositRequest $request)
    {
        $ub = $this->operationsService->deposit(
            $this->ubRepo->getOrNewByUserId($request->user_id),
            $request->amount,
            $request->comment
        );

        return (new UserBalanceResource($ub))->response()->setStatusCode(200);
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
