<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RewardStoreRequest;
use App\Http\Resources\RewardResource;
use App\Services\RewardService;
use http\Env\Request;
use Illuminate\Http\JsonResponse;

class RewardController extends Controller
{

    private RewardService $rewardService;

    /**
     * @param RewardService $rewardService
     */
    public function __construct(RewardService $rewardService)
    {

        $this->rewardService = $rewardService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $rewards = $this->rewardService->getAllRewards();

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'rewards' => RewardResource::collection($rewards)
        ]);
    }

    /**
     * @param RewardStoreRequest $request
     * @return JsonResponse
     */
    public function store(RewardStoreRequest $request): JsonResponse
    {
        $reward = $this->rewardService->create($request->all());

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'rewards' => new RewardResource($reward)
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $rewardDeleted = $this->rewardService->destroy($id);

        return response()->json([
            'success' => $rewardDeleted,
            'type'    => $rewardDeleted ? 'success' : 'error',
        ]);
    }


    /**
     * @param RewardStoreRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(RewardStoreRequest $request, int $id): JsonResponse
    {
        $rewardUpdated = $this->rewardService->update($id, $request->all());

        return response()->json([
            'success' => $rewardUpdated,
            'type'    => $rewardUpdated ? 'success' : 'error',
        ]);
    }
}
