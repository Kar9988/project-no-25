<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserReward;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserRewardService
{
    private UserReward $userRewardModel;
    private UserBalanceService $userBalanceService;
    private RewardService $rewardService;

    /**
     * @param UserReward $userRewardModel
     * @param RewardService $rewardService
     * @param UserBalanceService $userBalanceService
     */
    public function __construct(UserReward         $userRewardModel, RewardService $rewardService,
                                UserBalanceService $userBalanceService)
    {
        $this->userRewardModel = $userRewardModel;
        $this->userBalanceService = $userBalanceService;
        $this->rewardService = $rewardService;
    }

    /**
     * @param int $userId
     * @param string|null $date
     * @param int $page
     * @param int $take
     * @return Collection
     */
    public function showUserRewards(int $userId, null|string $date = null, int $page = 1, int $take = 10): Collection
    {

        return $this->userRewardModel
            ->select('user_reward.*')
            ->join('rewards', 'rewards.id', 'user_reward.reward_id')
            ->where('user_id', $userId)
            ->when($date, function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->whereDate('user_reward.created_at', $date)
                        ->orWhere('rewards.type', 'fb_social');
                });
            })
            ->with('reward')
            ->skip($page * $take - $take)
            ->take($take)
            ->get();
    }

    /**
     * @param int $userId
     * @param int $rewardId
     * @return UserReward
     */
    public function addUserReward(int $userId, int $rewardId): array
    {
        $reward = $this->rewardService->getById($rewardId);
        $userDailyRewards = $this->userRewardModel
            ->join('rewards', 'rewards.id', 'user_reward.reward_id')
            ->where('user_reward.user_id', $userId)
            ->when($reward->type == 'daily', function ($query) {
                $query->whereDate('user_reward.created_at', Carbon::today());
            })
            ->when($reward->type == 'daily_ad', function ($query) {
                $query->whereDate('user_reward.created_at', Carbon::today());
            })
            ->where('rewards.type', $reward->type)
            ->first();
        if ($userDailyRewards) {

            return [
                'success' => false,
                'type'    => 'error',
                'message' => 'You already got your reward'
            ];
        }
        $userBalance = $this->userBalanceService->getByUserId($userId);
        if (!$userBalance) {
            $userBalance = $this->userBalanceService->store([
                'user_id' => $userId,
                'amount'  => $reward->bonus
            ]);
        } else {
            $this->userBalanceService->update([
                'amount' => $reward->bonus + $userBalance->amount
            ], $userId);
        }
        $userReward = $this->userRewardModel->create([
            'user_id'   => $userId,
            'reward_id' => $rewardId
        ]);
        $userReward->load('reward');

        return [
            'success' => true,
            'type'    => 'success',
            'reward'  => $userReward
        ];
    }

}
