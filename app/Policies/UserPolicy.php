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
        foreach ($user->payments as $payment) {
            $paymentable_id = $payment->paymentable_id;
        }

        if ($user->role->name === 'admin') {
            return true;
        }

        return $paymentable_id === $episodeId;
    }
}
