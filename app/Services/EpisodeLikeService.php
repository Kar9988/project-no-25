<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Like;

class EpisodeLikeService
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($data): mixed
    {
        $video = Episode::find($data['episode_id']);
        return $video->likes()->create([
            'user_id' => auth()->user()->id,
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return Like::query()
            ->where('likeable_id', $id)
            ->where('user_id', auth()->user()->id)
            ->delete();
    }
}
