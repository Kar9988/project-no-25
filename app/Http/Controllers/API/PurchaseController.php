<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PaymentIntentRequest;
use App\Http\Requests\API\PlanPurchaseRequest;
use App\Http\Resources\PurchaseHistoryResource;
use App\Services\PurchaseService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private PurchaseService $purchaseService;
    private SubscriptionService  $subscriptionService;

    /**
     * @param PurchaseService $purchaseService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(PurchaseService $purchaseService, SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->purchaseService = $purchaseService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $result = $this->purchaseService->purchaseEpisode($request->video_id, auth()->id(), $request->type);

        return response()->json($result);
    }

    /**
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->purchaseService->getHistory(auth()->id(),  $request->get('page', 1),$request->get('take', 10));
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
        $response = $this->purchaseService->purchasePlan($request->payment_method, $request->user_id, $request->plan_id, $request->type);

        return response()->json($response);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function cancelSubscription(): JsonResponse
    {
        $cancel = $this->subscriptionService->cancelAuthUserActiveSubscription();
        if ($cancel) {
            return response()->json([
                'success' => true,
                'type' => 'success',
                'message' => 'Subscription cancelled successfully'
            ]);
        }
        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => 'Something is wrong'
        ]);
    }
}
