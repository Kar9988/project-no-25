<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function delete(UserService $service)
    {
        auth()->user()->token()->revoke();
        $res = $service->delete(auth()->id());
        if ($res) {
            return response()->json([
                'success' => true,
                'message' => 'account successfully deleted'
            ], 201);
        }
        return response()->json([
            'success' => false,
            'message' => 'account not deleted'
        ], 401);
    }
}
