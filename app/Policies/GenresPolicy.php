<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GenresPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAdmin(?User $user)
    {
        if ($user && $user->role_id == Role::ADMIN_ID) {
            return Response::allow('success');
        } else {
            return Response::deny('error');
        }
    }
}
