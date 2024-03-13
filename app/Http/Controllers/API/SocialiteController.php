<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SocialiteService;
use Illuminate\Http\JsonResponse;

class SocialiteController extends Controller
{
    /**
     * @param SocialiteService $service
     */
    public function __construct(protected SocialiteService $service)
    {

    }

    public function redirectToGoogle(): JsonResponse
    {
        $url = $this->service->redirectToGoogle();

        return response()->json([
            'type' => 'success',
            'url' => $url
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function handleGoogleCallback(): JsonResponse
    {
        $token = $this->service->handleGoogleCallback();
        return response()->json([
            'type' => 'success',
            'token' => $token
        ], 200);
    }
}
