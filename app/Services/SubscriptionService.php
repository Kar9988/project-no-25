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
    public function delete(int $id): bool
    {
        return $this->model->query()->where('id', $id)->delete();
    }
    public function getById(int $id): Subscription
    {
        return $this->model->query()->where('id', $id);
    }
}
