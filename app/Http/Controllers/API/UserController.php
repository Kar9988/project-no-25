<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param UserService $service
     * @return JsonResponse
     */
    public function delete(UserService $service): JsonResponse
    {
        auth()->user()->token()->revoke();
        if ($service->delete(auth()->id())) {

            return response()->json([
                'success' => true,
                'message' => 'Account successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ]);
    }
}
