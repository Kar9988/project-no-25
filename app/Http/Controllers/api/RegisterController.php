<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\RegisterReuest;
use App\Mail\VerifyMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class RegisterController extends Controller
{
    /**
     * @param RegisterReuest $request
     * @return JsonResponse
     */
    public function store(RegisterReuest $request): JsonResponse
    {
        $random = Str::random(40);

        $userData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::USER_ID,
            'email_verify_token' => $random
        ]);

        Mail::to($userData->email)->queue(new VerifyMail($random));

        return response()->json([
            'success' => true,
            'message' => 'user successfully registered'
        ], 201);
    }
}
