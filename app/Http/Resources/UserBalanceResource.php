<?php

namespace App\Http\Resources;

use App\Models\UserBalance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBalanceResource extends JsonResource
{
    function toArray(Request $request): array
    {
        /** @var UserBalance $ub */
        $ub = $this->resource;

        return [
            'user_id' => $ub->user_id,
            'balance' => round($ub->balance, 2),
            'transactions' => $this->whenLoaded(
                'transactions',
                fn () => TransactionResource::collection($ub->transactions)
            ),
        ];
    }
}
