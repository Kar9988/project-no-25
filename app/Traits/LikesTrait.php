<?php

namespace App\Traits;

use App\Http\Resources\LikeResource;
use App\Models\Episode;
use App\Models\Like;
use App\Models\Video;
use App\Services\LIkeService;
use Illuminate\Http\JsonResponse;

trait LikesTrait
{

    /**
     * @param LIkeService $service
     */

    public function __construct(protected LIkeService $service)
    {
    }

    /**
     * @param $datum
     * @return JsonResponse
     */
    public function like($datum): JsonResponse
    {
        $count = (int)$datum['likes_count'];
        if ($count > 0) {
            $data = [];
            for ($i = 0; $i < $count; $i++) {
                if (isset($datum['video_id'])) {
                    $data[] = [
                        'user_id' => auth()->id(),
                        'likeable_type' => Video::class,
                        'likeable_id' => $datum['video_id'],
                    ];
                }
                if (isset($datum['episode_id'])) {
                    $data[] = [
                        'user_id' => auth()->id(),
                        'likeable_type' => Episode::class,
                        'likeable_id' => $datum['episode_id'],
                    ];
                }
            }
            $this->service->insert($data);
            return response()->json([
                'success' => true,
                'message' => 'Likes created successfully',
            ], 201);
        } else {
            $create = $this->service->store($datum->all());
            return response()->json([
                'success' => true,
                'message' => 'Likes created successfully',
                'likes' => new LikeResource($create)
            ], 201);
        }
    }

    /**
     * @param $id
     * @param $data
     * @return JsonResponse
     */
    public function destroyLike($id, $data): JsonResponse
    {
        if (isset($data['episode_id'])) {
            if (isset($data['count']) && $data['count'] != null) {
                $deletedRows = Like::query()->where('likeable_id', $id)
                    ->where('user_id', auth()->id())
                    ->where('likeable_type', Episode::class)
                    ->take($data['count'])
                    ->delete();
                return response()->json(['message' => $deletedRows . ' records deleted successfully']);
            }
            return response()->json(['message' => 'There is no line to delete']);
        }
        if (isset($data['video_id'])) {
            if (isset($data['count']) && $data['count'] != null) {
                $deletedRows = Like::query()->where('likeable_id', $id)
                    ->where('user_id', auth()->id())
                    ->where('likeable_type', Video::class)
                    ->take($data['count'])
                    ->delete();
                return response()->json(['message' => $deletedRows . ' records deleted successfully']);
            }
            return response()->json(['message' => 'There is no line to delete']);
        }
    }
}
