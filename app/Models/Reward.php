<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [''];

    const DEFAULT_REWARDS = [
        'DAILY_REWARD' => [
            'type' => 'daily',
            'bonus' => 20,
        ],
        'DAILY_ADS_REWARD' => [
            'type' => 'daily_ad',
            'bonus' => 50
        ],
        'FB_REWARD' => [
            'type' => 'fb_social',
            'bonus' => 50
        ]
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_reward');
    }


}
