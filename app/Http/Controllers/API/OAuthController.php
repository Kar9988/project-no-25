<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use App\Models\OauthAccount;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpseclib3\Crypt\EC\Formats\Keys\Common;


class OAuthController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = User::firstOrCreate(
            ['email' => $request->user['email']],
            ['name' => $request->user['name'], 'role_id' => Role::USER_ID]
        );

        if ($user) {
            $oauthData = [
                'user_id' => $user->id,
                'idToken' => $request->idToken,
            ];

            switch ($request->type) {
                case 'google':
                    $oauthData['google_id'] = $request->user['id'];
                    break;
                case 'facebook':
                    $oauthData['facebook_id'] = $request->user['id'];
                    break;
                case 'apple':
                    $oauthData['apple_id'] = $request->user['id'];
                    break;
                default:
                    break;
            }

            OauthAccount::updateOrCreate(['user_id' => $user->id], $oauthData);
        }

        $token = OauthAccount::where('user_id', $user->id)->value('idToken');

        return response()->json([
            'success' => true,
            'user' => new AuthUserResource($user),
            'token' => $token,
            'type' => 'success'
        ]);
    }


}
