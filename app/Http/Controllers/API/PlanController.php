<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Http\Resources\PlanResource;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PlanController extends Controller
{
    /**
     * @param PlanService $service
     */
    public function __construct(protected PlanService $service)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => PlanResource::collection($this->service->index()),
            'success' => true,
            'type' => 'success',
            'message' => 'Plans retrieved successfully.',
        ]);
    }

    /**
     * @param PlanStoreRequest $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->service->store($request->all());
        if ($data) {
            return response()->json([
                'success' => true,
                'type' => 'success',
                'plan' => new PlanResource($data),
            ]);
        }
        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => 'Sorry, something went wrong.',
        ]);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $plan = $this->service->getById($id);
        if ($plan) {
            return response()->json([
                'success' => true,
                'type' => 'success',
                'plan' => new PlanResource($plan),
                'message' => 'Plan retrieved successfully.',
            ]);
        }
        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => 'Plan not found.',
        ]);
    }

    /**
     * @param PlanUpdateRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(PlanUpdateRequest $request, string $id): JsonResponse
    {
        $updateData = $this->service->update($request->all(), $id);
        if ($updateData === 1) {
            return response()->json([
                'success' => true,
                'type' => 'success',
                'message' => 'Plan successfully updated'
            ]);
        }

        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => $request->message
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
                'type' => 'success',
                'message' => 'Plan successfully deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => 'Plan could not be deleted'
        ]);
    }
}
