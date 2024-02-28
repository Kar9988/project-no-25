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
        if ($service->delete(auth()->id())) {
            return response()->json([
                'success' => true,
                'message' => 'Account successfully deleted'
            ], 201);
        }
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ]);
    }
}
