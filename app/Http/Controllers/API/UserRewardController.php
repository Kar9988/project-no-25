<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserRewardStoreRequest;
use App\Http\Resources\UserRewardResource;
use App\Services\UserRewardService;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserRewardController extends Controller
{
    private UserRewardService $userRewardService;

    /**
     * @param UserRewardService $userRewardService
     */
    public function __construct(UserRewardService $userRewardService)
    {

        $this->userRewardService = $userRewardService;
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        $rewards = $this->userRewardService->showUserRewards(auth()->id(), request()->get('date', null), request()->get('page', 1));

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'rewards' => UserRewardResource::collection($rewards)
        ]);
    }


    /**
     * @param UserRewardStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserRewardStoreRequest $request): JsonResponse
    {
        $result = $this->userRewardService->addUserReward(auth()->id(), (int)$request->get('reward_id'));
        if (!$result['success']) {

            return response()->json($result);
        }

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'rewards' => new UserRewardResource($result['reward'])
        ]);
    }
}
