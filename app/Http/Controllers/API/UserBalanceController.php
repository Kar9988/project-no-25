<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserBalanceRequest;
use App\Http\Resources\UserBalanceResource;
use App\Services\UserBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserBalanceController extends Controller
{
    /**
     * @param UserBalanceService $service
     */
    public function __construct(protected UserBalanceService $service)
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
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserBalanceRequest $request): JsonResponse
    {
        $data = $this->service->store($request->all());

        return response()->json([
            'success' => true,
            'message' => 'user balance created successfully',
            'user balance' => new UserBalanceResource($data)
        ],201);

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
            'plan' => new UserBalanceResource($plan)
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
                'message' => 'user balance updated successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'something was wrong'
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
                'message' => 'user balance deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'something was wrong'
        ]);
    }
}
