<?php

namespace App\Services;

use App\Models\View;

class ViewService
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($data): View
    {
        return View::create($data);
    }

    /**
     * @param int $episodeId
     * @param int $userId
     * @param int $count
     * @return mixed
     */
    public function createMany(int $episodeId, int $userId, int $count): mixed
    {
        $viewsData = [];
        for ($i = 0; $i < $count; $i++) {
            $viewsData[] = [
                'episode_id' => $episodeId,
                'user_id' => $userId,
            ];
        }

        return View::insert($viewsData);
    }
}
