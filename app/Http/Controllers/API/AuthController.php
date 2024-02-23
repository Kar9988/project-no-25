<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(): \Illuminate\Http\JsonResponse
    {
        $user =  auth()->user();
        return response()->json(['data' =>  new AuthUserResource($user), 'status' => 401]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()) {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out', 'success' => true, 'type' => 'success']);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}
