<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteService
{
    /**
     * @return mixed
     */
    public function redirectToGoogle(): mixed
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }

    /**
     * @return string
     */
    public function handleGoogleCallback(): string
    {
        $password = Str::random(10);
        $user = Socialite::driver('google')->stateless()->user();
        $existingUser = User::where('email', $user->email)->first();
        if ($existingUser) {
            Auth::login($existingUser);
            return $existingUser->createToken('GoogleToken')->accessToken;
        } else {
            $newUser = new User;
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->role_id = Role::USER_ID;
            $newUser->password = Hash::make($password);
            $newUser->google_id = $user->id;
            $newUser->save();
            Auth::login($newUser);
            return $newUser->createToken('GoogleToken')->accessToken;
        }
    }
}
