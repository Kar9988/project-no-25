<?php

namespace App\Policies;

use App\Models\User;
use Carbon\Carbon;

class SubscriptionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public static function subscrition($userId)
    {
        $users = User::with(['subscriptions' => function ($query) {
            $query->select('id', 'plan_id', 'user_id')
                ->where('end_date', '>=', now());
        }])->where('id', $userId)
            ->whereHas('subscriptions', function ($query) {
                $query->where('end_date', '>=', now());
            })
            ->first();
        if ($users) {
            $activeSubscription = [];
            foreach ($users->subscriptions as $sub) {
                $activeSubscription = [
                    'plan_id' => $sub->plan_id,
                    'sub_id' => $sub->id,
                    'active' => true,
                ];
            }
            return $activeSubscription;

        }
        return false;
    }
}
