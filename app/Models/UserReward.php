<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReward extends Model
{
    use HasFactory;

    protected $table = 'user_reward';

    protected $guarded = [''];

    /**
     * @return BelongsTo
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}
