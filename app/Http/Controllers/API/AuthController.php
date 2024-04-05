<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getAuthUser(): JsonResponse
    {

        return response()->json([
            'data' => new AuthUserResource(auth()->user()->load('role','getActiveSubscription')),
            'status' => true,
            'type'=>'success'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out',
                'success' => true,
                'type' => 'success'
            ]);
        }

        return response()->json([
            'message' => 'User not authenticated',
            'type'    => 'error'
        ], 401);
    }
}
