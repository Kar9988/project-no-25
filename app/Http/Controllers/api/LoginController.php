<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\LoginRequest;
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
                'user' => auth()->user(),
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
