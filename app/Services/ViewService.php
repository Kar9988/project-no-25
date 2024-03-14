<?php

namespace App\Services;

use App\Models\View;
use Illuminate\Support\Facades\DB;

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
    * @param $data
     * @return mixed
     */
    public function insert($data): mixed
    {
        return DB::table('views')->insert($data);
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

    /**
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(array $data, int $id): int
    {
        return View::query()->where('episode_id', $id)->update($data);
    }
}
