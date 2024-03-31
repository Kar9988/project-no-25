<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use App\Models\OauthAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OAuthController extends Controller
{

    public function __invoke(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->user??$request->all();
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name']??$data['first_name'], 'role_id' => Role::USER_ID]
            );

            if ($user) {
                $oauthData = [
                    'user_id' => $user->id,
                    'idToken' => $data['idToken']??$data['id'],
                ];

                switch ($data['type']) {
                    case 'google':
                        $oauthData['google_id'] = $data['id'];
                        break;
                    case 'facebook':
                        $oauthData['facebook_id'] = $data['id'];
                        break;
                    case 'apple':
                        $oauthData['apple_id'] = $data['id'];
                        break;
                    default:
                        break;
                }

                OauthAccount::updateOrCreate(['user_id' => $user->id], $oauthData);
            }

            if (auth::loginUsingId($user->id)){
                $token = auth()->user()->createToken('API Token')->accessToken;
                DB::commit();
                return response()->json([
                    'success' => true,
                    'user' => new AuthUserResource($user),
                    'token' => $token,
                    'type' => 'success'
                ]);
            }else{
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'type' => 'error',
                    'message' => 'Something went wrong'
                ]);
            }
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'type' => 'error',
                'message' => $exception->getMessage()
            ]);
        }

    }


}
