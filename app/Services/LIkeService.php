<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Like;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class LIkeService
{
    /**
     * @param $data
     * @return Like
     */
    public function store($data): Like
    {
        if ($data['episode_id']) {
            $video = Episode::find($data['episode_id']);
            return $video->likes()->create([
                'user_id' => auth()->user()->id,
            ]);
        }
        if ($data['video_id']) {
            $video = Video::find($data['video_id']);
            return $video->likes()->create([
                'user_id' => auth()->user()->id,
            ]);
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($datum): mixed
    {
        return DB::table('likes')->insert($datum);
    }

    public function deleteLike($likeAbleId, $count, $type)
    {
        $deleteQuery = Like::query()->where('likeable_id', $likeAbleId)
            ->where([
                'user_id' => auth()->id(),
                'likeable_type' => $type
            ]);
        if ($count) {
            $deleteQuery->take($count);
        }
        return $deleteQuery->delete();

    }
}
