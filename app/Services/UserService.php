<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id): mixed
    {
        return User::query()->where('id', $id)->delete();
    }
}
