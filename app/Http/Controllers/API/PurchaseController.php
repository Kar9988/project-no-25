<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private PurchaseService $purchaseService;

    /**
     * @param PurchaseService $purchaseService
     */
    public function __construct(PurchaseService $purchaseService)
    {

        $this->purchaseService = $purchaseService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $result = $this->purchaseService->purchaseEpisode($request->video_id, auth()->id());

        return response()->json($result);
    }
}
