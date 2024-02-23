<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['data' => auth()->user(), 'status' => 401]);
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
