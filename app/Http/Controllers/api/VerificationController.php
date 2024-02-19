<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use MustVerifyEmail;
    /**
     * @param $token
     * @return void
     */
    public function verify($token): void
    {
        User::where('email_verify_token', $token)->update(['email_verified_at' => Carbon::now()]);
    }
}
