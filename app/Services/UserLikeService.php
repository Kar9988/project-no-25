<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Video;

class UserLikeService
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($data): mixed
    {
        $video = Video::find($data['video_id']);
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
            ->where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->delete();
    }
}
