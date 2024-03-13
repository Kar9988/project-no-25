<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\BalanceRequest;
use App\Http\Requests\API\UserBalanceRequest;
use App\Http\Resources\UserBalanceResource;
use App\Services\PaymentService;
use App\Services\UserBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserBalanceController extends Controller
{
    /**
     * @param UserBalanceService $service
     */
    public function __construct(protected UserBalanceService $service, protected PaymentService  $paymentService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => UserBalanceResource::collection($this->service->index()),
            'success' => true,
            'type'    => 'success'
        ], 200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $plan = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'plan' => new UserBalanceResource($plan),
            'type' => 'success'
        ],200);
    }

    /**
     * @param UserBalanceRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UserBalanceRequest $request, string $id): JsonResponse
    {
        $updateData = $this->service->update($request->all(), $id);
        if ($updateData) {
            return response()->json([
                'success' => true,
                'message' => 'user balance updated successfully',
                'type'    => 'success'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'something was wrong',
            'type'    => 'error'
        ]);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        if ($this->service->delete($id) === 1) {

            return response()->json([
                'success' => true,
                'message' => 'user balance deleted successfully',
                'type'    => 'success'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'something was wrong',
            'type'    => 'error'
        ]);
    }

    /**
     * @param BalanceRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function store(BalanceRequest $request, int $userId): JsonResponse
    {
        $updateBalance = $this->service->addBalance($request->all(), $userId);
        if ($updateBalance['success'] === true) {
            return response()->json([
                'type' => 'success',
                'success' => true,
                'message' => 'user balance updated successfully'
            ]);
        };
        return response()->json([
            'type'=>'error',
            'success' => false,
            'message' => 'something was wrong'
        ]);
    }
}
