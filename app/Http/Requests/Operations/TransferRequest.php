<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $from_user_id
 * @property int $to_user_id
 * @property float $amount
 * @property string|null $comment
 */
class TransferRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'from_user_id' => 'required|integer|min:1',
            'to_user_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:1',
            'comment' => 'nullable|string',
        ];
    }
}
