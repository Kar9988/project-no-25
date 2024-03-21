<?php

declare(strict_types=1);

namespace App\Services;
use App\Models\UserReward;
use Illuminate\Support\Collection;

class UserRewardService
{
    private UserReward $userRewardModel;

    /**
     * @param UserReward $userRewardModel
     */
    public function __construct(UserReward $userRewardModel)
    {
        $this->userRewardModel = $userRewardModel;
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

        return $this->userRewardModel->where('user_id', $userId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->with('reward')
            ->skip($page*$take-$take)
            ->take($take)
            ->get();
    }

    /**
     * @param int $userId
     * @param int $rewardId
     * @return UserReward
     */
    public function addUserReward(int $userId, int $rewardId): UserReward
    {
        $reward = $this->userRewardModel->create([
            'user_id' => $userId,
            'reward_id' => $rewardId
        ]);
        $reward->load('reward');

        return $reward;
    }

}
