<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $service;

    /**
     * @param PlanService $service
     */
    public function __construct(PlanService $service)
    {
        $this->service = $service;
    }


    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => PlanResource::collection($this->service->index()), 'success' => true, 'type' => 'success']);
    }

    /**
     * @param PlanRequest $request
     * @return JsonResponse
     */
    public function store(PlanRequest $request): JsonResponse
    {
        $data = $this->service->store($request->all());
        return response()->json(['success' => true, 'type' => 'success', 'plan' => new PlanResource($data)]);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $plan = $this->service->getById($id);
        return response()->json(['success' => true, 'type' => 'success', 'plan' => new PlanResource($plan)]);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $updateData = $this->service->update($request->all(), $id);
        if ($updateData === 1) {
            return response()->json(['success' => true, 'type' => 'success']);
        }
        return response()->json(['success' => false, 'type' => 'error']);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        if ($this->service->delete($id) === 1) {
            return response()->json(['success' => true, 'type' => 'success']);
        };
        return response()->json(['success' => false, 'type' => 'error']);

    }
}
