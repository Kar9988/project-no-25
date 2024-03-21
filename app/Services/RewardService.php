<?php

declare(strict_types=1);

namespace App\Services;
use App\Models\Reward;
use Illuminate\Database\Eloquent\Collection;

class RewardService
{
    private Reward $reward;

    /**
     * @param Reward $reward
     */
    public function __construct(Reward $reward)
    {
        $this->reward = $reward;
    }

    /**
     * @return Collection
     */
    public function getAllRewards(): Collection
    {
        return $this->reward->all();
    }

    /**
     * @param array $data
     * @return Reward
     */
    public function create(array $data): Reward
    {
        return $this->reward->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->reward->where('id', $id)
            ->update($data);
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy(int $id): bool
    {

        return $this->reward->where('id', $id)
            ->delete();
    }

}
