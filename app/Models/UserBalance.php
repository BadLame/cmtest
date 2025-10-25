<?php

namespace App\Models;

use Database\Factories\UserBalanceFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property float $balance
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property Collection<Transaction> $transactions
 *
 * @method static UserBalanceFactory factory($count = null, $state = [])
 */
class UserBalance extends Model
{
    use HasFactory;

    protected $table = 'users_balances';

    protected $fillable = [
        'user_id',
    ];

    protected $attributes = [
        'balance' => 0,
    ];

    // Relations

    function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }
}
