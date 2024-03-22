<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\AuthUserResource;
use Carbon\Carbon;
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
                'success' => true,
                'user'    => new AuthUserResource(auth()->user()),
                'token'   => $token,
                'type'    =>'success'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Wrong email or password',
                'type'    =>'error'
            ], 401);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = Auth::user()->token();
        $user->revoke();

        return response()->json([
            'success' => true,
            'type'    =>'success'
        ]);
    }
}
