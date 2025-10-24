<?php

namespace App\Models;

use App\Models\Enums\TransactionType;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property TransactionType $type
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property UserBalance $balance
 *
 * @method static TransactionFactory factory($count = null, $state = [])
 */
class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => TransactionType::class,
    ];

    // Relations

    function balance(): BelongsTo
    {
        return $this->belongsTo(UserBalance::class, 'user_id', 'user_id');
    }
}
