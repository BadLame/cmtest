<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransactionException;
use App\Http\Requests\Operations\DepositOrWithdrawRequest;
use App\Http\Requests\Operations\TransferRequest;
use App\Http\Resources\UserBalanceResource;
use App\Repository\UserBalance\UserBalanceRepository;
use App\Service\Operations\OperationsService;

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

    /** @throws TransactionException */
    function deposit(DepositOrWithdrawRequest $request)
    {
        $ub = $this->operationsService->deposit(
            $this->ubRepo->getOrNewByUserId($request->user_id),
            $request->amount,
            $request->comment
        );

        return (new UserBalanceResource($ub))->response()->setStatusCode(200);
    }

    /** @throws InsufficientFundsException */
    function withdraw(DepositOrWithdrawRequest $request)
    {
        $ub = $this->operationsService->withdraw(
            $this->ubRepo->getByUserId($request->user_id),
            $request->amount,
            $request->comment
        );

        return (new UserBalanceResource($ub))->response()->setStatusCode(200);
    }

    function transfer(TransferRequest $request)
    {
        $ub = $this->operationsService->transfer(
            $this->ubRepo->getByUserId($request->from_user_id),
            $this->ubRepo->getByUserId($request->to_user_id),
            $request->amount,
            $request->comment
        );

        return (new UserBalanceResource($ub))->response()->setStatusCode(200);
    }
}
