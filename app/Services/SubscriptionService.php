<?php

namespace App\Services;

use App\Models\Subscription;

class SubscriptionService
{
    /**
     * @param Subscription $model
     */
    public function __construct(protected Subscription $model)
    {

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
