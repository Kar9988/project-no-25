<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RewardResource;
use App\Services\RewardService;
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
}
