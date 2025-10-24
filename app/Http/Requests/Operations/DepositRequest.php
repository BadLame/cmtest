<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $user_id
 * @property float $amount
 * @property string|null $comment
 */
class DepositRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'user_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:1',
            'comment' => 'nullable|string|max:255',
        ];
    }
}
