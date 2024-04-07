<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;
use Carbon\Carbon;

class UserPolicy
{

    /**
     * @param $user
     * @param int|null $episodeId
     * @return bool
     */
    public static function canViewEpisode($user, int $episodeId = null): bool
    {
        if ($episodeId) {
            $episode = Episode::where('id', $episodeId)->first();
            if ($episode && $episode->price == 0) {
                return true;
            }
        }
        if ($episodeId != null) {
            $payment = $user->payments()->where('paymentable_id', $episodeId)
                ->where('paymentable_type', Episode::class)
                ->first();
            if ($payment !== null) {
                return true;
            }
        }

        if ($user?->role?->name === 'admin') {
            return true;
        }
        $activeSubscriptions = $user->getActiveSubscription()->first();
        if ($activeSubscriptions) {
            return true;
        }

        return false;
    }
}
