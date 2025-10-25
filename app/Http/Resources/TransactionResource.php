<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    function toArray(Request $request): array
    {
        /** @var Transaction $t */
        $t = $this->resource;

        return [
            'id' => $t->id,
            'user_id' => $t->user_id,
            'amount' => $t->amount,
            'type' => $t->type->value,
            'comment' => $t->comment,
        ];
    }
}
