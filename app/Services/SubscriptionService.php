<?php

namespace App\Services;

use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * @param Subscription $model
     */
    public function __construct(protected Subscription $model)
    {

    }

    /**
     * @param int $userId
     * @return bool
     */
    public function cancelAuthUserActiveSubscription(int $userId): bool
    {
        $subscription = $this->model->query()->where('user_id', $userId)
            ->where('end_date', '>=', Carbon::now())
            ->whereNull('cancelled_at')
            ->first();

        return $subscription->update([
            'cancelled_at' => Carbon::now(),
        ]);
    }

    /**
     * @param array $data
     * @return Subscription
     */
    public function store(array $data): Subscription
    {
        return Subscription::create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update(int $id, array $data): int
    {
        return $this->model->query()->where('id', $id)->update($data);
    }

}
