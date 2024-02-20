<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    /**
     * @param $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return Plan::create($data);
    }

    /**
     * @return mixed
     */
    public function index():mixed
    {
        return Plan::all();
    }

    /**
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(array $data, int $id): int
    {
        return Plan::query()->where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id):mixed
    {
        return Plan::query()->findOrFail($id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return Plan::query()->where('id', $id)->delete();
    }
}
