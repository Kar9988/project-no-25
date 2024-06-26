<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [];

    const CARD_TYPES = [
        'master_card' => 'Master Card',
        'visa_card' => 'Visa Card',
        'apple_pay' => 'Apple Pay',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }


    /**
     * @return HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return HasOne
     */
    public function getActiveSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('end_date', '>=', Carbon::now())->whereNull('cancelled_at');
    }

    /**
     * @return BelongsToMany
     */
    public function rewards(): BelongsToMany
    {
        return $this->belongsToMany(Reward::class, 'user_reward');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function historys()
    {
        return $this->hasMany(UserEpisodesHistory::class);
    }

    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    /**
     * @return HasOne
     */
    public function userBalance(): HasOne
    {
        return $this->hasOne(UserBalance::class);
    }

    public function card()
    {
        return $this->hasOne(UserCard::class);
    }


    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
