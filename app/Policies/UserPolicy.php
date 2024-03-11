<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    /**
     * @param User $user
     * @param int $episodeId
     * @return bool
     */
    public static function canViewEpisode(User $user, int $episodeId): bool
    {
        $payment = $user->payments()->where('paymentable_id', $episodeId)->first();
        if ($payment !== null) {
            return true;
        }

        if ($user->role->name === 'admin') {
            return true;
        }
        return false;
    }
}
