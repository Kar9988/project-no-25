<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\AuthUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $data = $request->except('_token');
        if (Auth::attempt($data)) {
            $token = auth()->user()->createToken('API Token')->accessToken;

            return response()->json([
                'user' => new AuthUserResource(auth()->user()),
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'something was wrong'
            ], 401);
        }
    }
}
