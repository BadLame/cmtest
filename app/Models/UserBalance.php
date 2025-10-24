<?php

namespace App\Models;

use Database\Factories\UserBalanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property float $balance
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @method static UserBalanceFactory factory($count = null, $state = [])
 */
class UserBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    protected $attributes = [
        'balance' => 0,
    ];

    protected $table = 'users_balances';
}
