<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function delete($id)
    {
        return User::query()->where('id', $id)->delete();
    }
}
