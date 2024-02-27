<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;

class AdminService
{
    /**
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(array $data, int $id): int
    {
        return User::query()->where('id', $id)->update($data);
    }
}
