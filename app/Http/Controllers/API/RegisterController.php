<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterReuest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{
    /**
     * @param RegisterReuest $request
     * @return JsonResponse
     */
    public function store(RegisterReuest $request): JsonResponse
    {

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::USER_ID,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'user successfully registered',
            'type'    => 'success'
        ], 201);
    }
}
