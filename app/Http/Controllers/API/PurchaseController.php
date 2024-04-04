<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PaymentIntentRequest;
use App\Http\Requests\API\PlanPurchaseRequest;
use App\Http\Resources\PurchaseHistoryResource;
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

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $result = $this->purchaseService->getHistory(auth()->id());
        if ($result){
            return response()->json([
                'success' => true,
                'data'    => PurchaseHistoryResource::collection($result),
                'type'    => 'success'
            ]);
        }
        return response()->json([
            'success' => false,
            'type'    => 'error',
            'message' => 'Something is wrong'
        ]);
    }

    /**
     * @param PaymentIntentRequest $request
     * @return JsonResponse
     */
    public function applePaymentIntent(PaymentIntentRequest $request): JsonResponse
    {
        $clientId = $this->purchaseService->createPaymentIntent($request->plan_id);

        return response()->json([
            'success'   => true,
            'type'      => 'success',
            'client_id' => $clientId
        ]);
    }

    /**
     * @param PlanPurchaseRequest $request
     * @return JsonResponse
     */
    public function storePlan(PlanPurchaseRequest $request): JsonResponse
    {
        $response = $this->purchaseService->purchasePlan($request->payment_method, $request->payment_id,
            $request->user_id, $request->plan_id, $request->type);

        return response()->json($response);
    }
}
