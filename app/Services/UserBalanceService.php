<?php

namespace App\Services;

use App\Models\UserBalance;

class UserBalanceService
{
    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return UserBalance::create($data);
    }

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return UserBalance::all();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return UserBalance::query()->findOrFail($id);
    }


    /**
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(array $data, int $id): int
    {
        return UserBalance::query()->where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return UserBalance::query()->where('id', $id)->delete();
    }

}
