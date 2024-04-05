<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;
use Carbon\Carbon;

class UserPolicy
{

    /**
     * @param User $user
     * @param int $episodeId
     * @return bool
     */
    public static function canViewEpisode(User $user, int $episodeId): bool
    {
        $payment = $user->payments()->where('paymentable_id', $episodeId)
            ->where('paymentable_type', Episode::class)->first();
        if ($payment !== null) {
            return true;
        }

        if ($user?->role?->name === 'admin') {
            return true;
        }
        return false;
    }
    public static function isActiveSubscription($user_id): array
    {
        $user = User::query()->where('id', $user_id)->firstOrFail();
        $data = [
            'active' => false,
        ];
        $activeSubscriptions = $user->subscriptions()->where('end_date', '>=', Carbon::now())->first();
        if ($activeSubscriptions){
            return $data = [
                'active' => true,
                'subscription_id' => $activeSubscriptions->id,
                'plan_id' => $activeSubscriptions->plan_id,
            ];
        }
        return $data;
    }
}
